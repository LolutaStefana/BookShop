import React, { useState, useEffect } from 'react';
import {useParams, useNavigate, Link, Navigate} from 'react-router-dom';
import api from '../api';
import '../../css/update.css';
import {useAuth} from "../../useAuth";
import Swal from "sweetalert2";

function UpdateOrganization() {
    const { id } = useParams();
    const isAuthenticated = useAuth();
    const navigate = useNavigate();
    const [organization, setOrganization] = useState({
        name: '',
    });


    useEffect(() => {
        api
            .get(`/organisations/${id}`)
            .then((response) => {
                setOrganization(response.data);
            })
            .catch((error) => {
                console.error('Error fetching organization:', error);
            });
    }, [id]);

    const handleUpdate = () => {
        api
            .put(`/organisations/${id}`, organization)
            .then(() => {
                Swal.fire('Organisation updated successfully!','',"success");
                navigate('/showOrganizations');
            })
            .catch((error) => {
                console.error('Error updating organization:', error);
            });
    };
    return (
        <div>
            <h2 className="title1">Update Organization</h2>
            <div>
                <label>Name:</label>
                <input
                    type="text1"
                    value={organization.name}
                    onChange={(e) =>
                        setOrganization({ ...organization, name: e.target.value })
                    }
                />
            </div>
            <button type="button1" className={'button'} onClick={handleUpdate}>
                Update
            </button>
            <Link to="/showOrganizations">Cancel</Link>
        </div>
    );
}

export default UpdateOrganization;