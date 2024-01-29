import React, { useEffect, useState } from "react";
import {Link, Navigate, useNavigate} from "react-router-dom";
import api from "../api";
import "../../css/show.css";
import {useAuth} from "../../useAuth";
import Swal from "sweetalert2";

function ShowOrganizations() {
    const navigate = useNavigate();
    const isAuthenticated = useAuth();
    const [organizations, setOrganizations] = useState([]);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 3;
    const userId = "/users/" + localStorage.getItem("userId");
    const [selectedOrganization, setSelectedOrganization] = useState("");
    const [isJoinDisabled, setIsJoinDisabled] = useState(true);

    useEffect(() => {
        api
            .get(`/organisations?page=${currentPage}&itemsPerPage=${itemsPerPage}`)
            .then((response) => {
                setOrganizations(response.data["hydra:member"]);
                setLoading(false);
            })
            .catch((error) => {
                console.error("Error fetching organizations:", error);
                setLoading(false);
            });
    }, [currentPage, itemsPerPage]);

    useEffect(() => {
        setIsJoinDisabled(!selectedOrganization);
    }, [selectedOrganization]);

    if (loading) {
        return <div>Loading...</div>;
    }

    const handleDelete = (id) => {
        Swal.fire({
            title: 'Do you want to delete this organization?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                api
                    .delete(`/organizations/${id}`)
                    .then(() => {
                        setOrganizations(organizations.filter((org) => org.id !== id));
                        Swal.fire('Deleted!', 'The organization has been deleted.', 'success');
                    })
                    .catch((error) => {
                        console.error("Error deleting organization:", error);
                        Swal.fire({
                            title: "Permission denied",
                            text: "You are not authorized to delete this organization!",
                            icon: "error",
                        });
                    });
            }
        });
    };

    const handleUpdate = (id, ownerId) => {
        if (userId === ownerId) {
            navigate(`/updateOrganisation/${id}`);
        } else {

                Swal.fire({
                    title: "Permission denied",
                    text: "You are not authorized to update this organisation!",
                    icon: "error",
                });
        }
    };

    const handleOrganizationReport = (id) => {
        api
            .get(`/organisation/${id}/report`)
            .then((response) => {
                Swal.fire({
                    title: `Organisation Report`,
                    icon: 'info',
                    html: `
                    <p><strong>Organisation:</strong> ${response.data.organisation}</p>
                    <p><strong>Number of Liked Books:</strong> ${response.data.number_of_liked_books}</p>
                `,
                });
            })
            .catch((error) => {
                console.error("Error generating organisation report:", error);
                Swal.fire({
                    title: "Permission denied",
                    text: "You don't have the right to see this report.",
                    icon: "error",
                });
            });
    };

    const handleAdd = () => {
        navigate(`/addOrganization`);
    };

    const handleOrganizationSelect = (organizationId) => {
        if (selectedOrganization === organizationId) {
            setSelectedOrganization("");
        } else {
            setSelectedOrganization(organizationId);
        }
    };

    const handleJoinOrganization = () => {
        const userId = localStorage.getItem("userId");


        Swal.fire({
            title: 'Do you want to save the changes?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Save',
            denyButtonText: `No`,
        }).then((result) => {
            if (result.isConfirmed) {
                const requestData = {
                    organisation: "/organisations/" + selectedOrganization,
                };
                const headers = {
                    "Content-Type": "application/merge-patch+json",
                };
                api
                    .patch(`/users/${userId}`, requestData, { headers })
                    .then((response) => {
                        Swal.fire('Saved!', '', 'success')
                    })
                    .catch((error) => {
                        console.error("Error joining organization:", error);
                    });
            }
        });

    };
    if (!isAuthenticated) {

        return <Navigate to="/" />;
    }


    return (
        <div>
            <h2 className="title1">List of organisations</h2>
            <button type="add-button" onClick={() => handleAdd()}>
                Add new organisation
            </button>
            <table className="book-table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Actions</th>
                    <th>Report</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {organizations.map((org) => (
                    <tr key={org.id}>
                        <td>{org.name}</td>
                        <td>
                            <button
                                type="action-button"
                                onClick={() => handleDelete(org.id)}
                            >
                                Delete
                            </button>
                            <button
                                type="action-button"
                                onClick={() => handleUpdate(org.id, org.owner)}
                            >
                                Update
                            </button>

                        </td>
                        <td>
                            <button
                                type="action-button"
                                onClick={() => handleOrganizationReport(org.id)}
                            >
                                Generate Report
                            </button>
                        </td>
                        <td>
                            <button

                                onClick={() => {
                                    handleOrganizationSelect(org.id);
                                }}
                                className={selectedOrganization === org.id ? "selected" : ""}
                            >
                                Select
                            </button>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>

            <div className="pagination">
                <button
                    onClick={() => setCurrentPage(currentPage - 1)}
                    disabled={currentPage === 1}
                >
                    Previous
                </button>
                Page {currentPage}
                <button
                    onClick={() => setCurrentPage(currentPage + 1)}
                    disabled={organizations.length === 0 || organizations.length < itemsPerPage}
                >
                    Next
                </button>
            </div>

            <button
                type="add-button"
                onClick={handleJoinOrganization}
                className={isJoinDisabled ? "cloudy" : ""}
                disabled={isJoinDisabled}
            >
                Join Selected Organisation
            </button>

            <Link to="/success" className="act-button">
                Go back
            </Link>
        </div>
    );
}

export default ShowOrganizations;
