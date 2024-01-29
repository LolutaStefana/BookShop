
import React, { useEffect, useState } from "react";
import api from "../api";
import "../../css/show.css";
import {Link, Navigate, useNavigate} from "react-router-dom";
import {useAuth} from "../../useAuth";
import Swal from "sweetalert2";


function ShowReviews() {
    const navigate = useNavigate();
    const isAuthenticated = useAuth();
    const [reviews, setReviews] = useState([]);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 3;
    const userId = "/users/"+localStorage.getItem("userId");


    useEffect(() => {

        api
            .get(`/reviews?page=${currentPage}&itemsPerPage=${itemsPerPage}`)
            .then((response) => {
                setReviews(response.data["hydra:member"]);
                setLoading(false);
            })
            .catch((error) => {
                console.error("Error fetching reviews:", error);
                setLoading(false);
            });
    }, [currentPage, itemsPerPage]);

    if (loading) {
        return <div>Loading...</div>;
    }

    const handleDelete = (id) => {
        Swal.fire({
            title: 'Do you want to delete this review?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                api
                    .delete(`/reviews/${id}`)
                    .then(() => {
                        setReviews(reviews.filter((review) => review.id !== id));
                        Swal.fire('Review deleted successfully!', '', 'success');
                    })
                    .catch((error) => {
                        console.error("Error deleting review:", error);
                        Swal.fire({
                            title: "Permission denied",
                            text: "You are not authorized to delete this review!",
                            icon: "error",
                        });
                    });
            }
        });
    };

    const handleUpdate = (id,ownerId) => {
        if (userId === ownerId) {
            navigate(`/updateReview/${id}`);
        } else {

                Swal.fire({
                    title: "Permission denied",
                    text: "You are not authorized to update this review!",
                    icon: "error",
                });
        }
    };
    if (!isAuthenticated) {

        return <Navigate to="/" />;
    }

    return (
        <div>
            <h2 className="title1">List of reviews</h2>
            <table className="book-table">
                <thead>
                <tr>
                    <th>Author</th>
                    <th>Body</th>
                    <th>Rating</th>
                    <th>Publication Date</th>
                    <th>Book Title</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                {reviews.map((review) => (
                    <tr key={review.id}>
                        <td>{review.author}</td>
                        <td>{review.body}</td>
                        <td>{review.rating}</td>
                        <td>{review.publicationDate}</td>
                        <td>{review.book?.title || 'No associated book'}</td>
                        <td>
                            <button
                                type="action-button"
                                onClick={() => handleDelete(review.id)}
                            >
                                Delete
                            </button>
                            <button
                                type="action-button"
                                onClick={() => handleUpdate(review.id,review.owner)}
                            >
                                Update
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
                    disabled={reviews.length===0 || reviews.length <itemsPerPage}
                >
                    Next
                </button>
            </div>
            <Link to="/success" className="act-button">
                Go back
            </Link>
        </div>
    );
}

export default ShowReviews;