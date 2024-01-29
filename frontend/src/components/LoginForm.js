import React, { useState } from "react";
import axios from "axios";
import { Link, useNavigate } from "react-router-dom";
import api from "./api";

const baseURL = "https://localhost/auth";

function LoginForm() {
    const navigate = useNavigate();
    localStorage.removeItem('token');
    const [loginForm, setLoginForm] = useState({
        email: "",
        password: "",
    });

    const [errors, setErrors] = useState({});


    const isEmailValid = (email) => {
        const emailPattern = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
        return emailPattern.test(email);
    };

    const isPasswordValid = (password) => {
        const passwordPattern = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})/;
        return passwordPattern.test(password);
    };

    const handleLoginChange = (e) => {
        const { name, value } = e.target;
        setLoginForm({
            ...loginForm,
            [name]: value,
        });
    };

    const handleLoginSubmit = (e) => {
        e.preventDefault();

        const errors = {};

        if (!loginForm.email.trim() || !isEmailValid(loginForm.email)) {
            errors.email = "Please enter a valid email address";
        }

        if (!loginForm.password.trim() || !isPasswordValid(loginForm.password)) {
            errors.password = "Please enter a valid password";
        }

        setErrors(errors);

        if (Object.keys(errors).length === 0) {
            const requestData = {
                email: loginForm.email,
                password: loginForm.password,
            };

            axios
                .post(baseURL, requestData)
                .then((response) => {
                    const token = response.data.token;
                    localStorage.setItem('token', token);
                    api.get("/credentials")
                        .then((userResponse) => {
                            const userData = userResponse.data;
                            localStorage.setItem('userId', userData.id);
                            localStorage.setItem('email', userData.email);
                            localStorage.setItem('firstName', userData.firstName);
                            navigate('/success');
                        })
                        .catch((error) => {
                            console.error("Error fetching user credentials:", error);
                        });
                })
                .catch((error) => {
                    console.error("Login failed:", error);
                });
        }
    };

    return (
        <div className="login-form">
            <h2>Login</h2>
            <form onSubmit={handleLoginSubmit}>
                <div>
                    <input
                        type="email"
                        name="email"
                        placeholder="Email"
                        value={loginForm.email}
                        onChange={handleLoginChange}
                    />
                    {errors.email && <div className="error">{errors.email}</div>}
                </div>
                <div>
                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        value={loginForm.password}
                        onChange={handleLoginChange}
                    />
                    {errors.password && <div className="error">{errors.password}</div>}
                </div>
                <button type="submit">Login</button>
                <p>
                    Don't have an account?{" "}
                    <Link to="/register">Register</Link>
                </p>
            </form>
        </div>
    );
}

export default LoginForm;
