import React, { useState } from 'react';
import {Link, Navigate, useNavigate} from 'react-router-dom';
import api from '../api';
import {useAuth} from "../../useAuth";
import Swal from "sweetalert2";
function AddOrganization() {
    const navigate = useNavigate();
    const isAuthenticated = useAuth();
    const [organizationData, setOrganizationData] = useState({
        name: '',
    });

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setOrganizationData({
            ...organizationData,
            [name]: value,
        });
    };

    const handleFormSubmit = (e) => {
        e.preventDefault();

        api.post('/organisations', organizationData)
            .then(() => {
                console.log('Organization added successfully');
                Swal.fire('Organisation added successfully!','',"success");
                navigate('/showOrganizations');
            })
            .catch((error) => {
                console.error('Error adding organization:', error);
            });
    };
    return (
        <div className="add-organization">
            <h2 className="title1">Add Organization</h2>
            <form onSubmit={handleFormSubmit}>
                <div>
                    <input
                        type="text"
                        name="name"
                        placeholder="Name"
                        value={organizationData.name}
                        onChange={handleInputChange}
                    />
                </div>
                <button type="submit">Add Organization</button>
                <Link to="/showOrganizations">Cancel</Link>
            </form>
        </div>
    );
}

export default AddOrganization;
