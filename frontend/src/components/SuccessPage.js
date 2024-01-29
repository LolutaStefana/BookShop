import React from "react";
import "../css/succes.css";
import { Link } from "react-router-dom";
import { useAuth } from "../useAuth";

const SuccessPage = () => {
    const firstName = localStorage.getItem('firstName');
    const isAuthenticated = useAuth();
    const handleLogout = () => {
        localStorage.removeItem("token");
        localStorage.removeItem("firstName");
    };

    return (
        <div className="container">
            {isAuthenticated && (
                <div className="button-container">
                    <h1>Hi {firstName}!</h1>
                    <Link to="/showBooks" className="success-button">
                        Show Books
                    </Link>
                    <Link to="/showReviews" className="success-button">
                        Show Reviews
                    </Link>
                    <Link to="/showOrganizations" className="success-button">
                        Show Organisations
                    </Link>
                    <Link to="/" onClick={handleLogout}>
                        Logout
                    </Link>
                </div>
            )}
            {!isAuthenticated && (
                <div>
                <p>You are not logged in, click  <Link to="/" onClick={handleLogout}>
                    Here
                </Link> to login </p>
                </div>
            )}
        </div>
    );
};

export default SuccessPage;
