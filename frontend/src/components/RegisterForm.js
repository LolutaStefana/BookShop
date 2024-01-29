import React, { useState } from "react";
import axios from "axios";
import {Link, useNavigate} from "react-router-dom";

const baseURL = "https://localhost/register";

function RegisterForm() {
    const navigate = useNavigate();
    const [registerForm, setRegisterForm] = useState({
        firstName: "",
        lastName: "",
        email: "",
        password: "",
    });

    const [errors, setErrors] = useState({});
    const [message, setMessage] = useState("");

    const isEmailValid = (email) => {
        const emailPattern = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
        return emailPattern.test(email);
    };

    const isPasswordValid = (password) => {
        const passwordPattern = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})/;
        return passwordPattern.test(password);
    };

    const handleRegisterChange = (e) => {
        const { name, value } = e.target;
        setRegisterForm({
            ...registerForm,
            [name]: value,
        });
    };

    const handleRegisterSubmit = (e) => {
        e.preventDefault();

        const errors = {};

        if (!registerForm.firstName.trim()) {
            errors.firstName = "First name cannot be empty";
        }

        if (!registerForm.lastName.trim()) {
            errors.lastName = "Last name cannot be empty";
        }

        if (!registerForm.email.trim() || !isEmailValid(registerForm.email)) {
            errors.email = "Please enter a valid email address";
        }

        if (
            !registerForm.password.trim() ||
            !isPasswordValid(registerForm.password)
        ) {
            errors.password =
                "Password must be at least 8 characters, contain one special character, one number, and one uppercase letter";
        }

        setErrors(errors);

        if (Object.keys(errors).length === 0) {
            const requestData = {
                firstName: registerForm.firstName,
                lastName: registerForm.lastName,
                email: registerForm.email,
                plainPassword: registerForm.password,
            };

            axios
                .post(baseURL, requestData)
                .then((response) => {
                    setMessage("Registration successful");
                    console.log("Registration successful:", response.data);
                    navigate('/');
                })
                .catch((error) => {
                    setMessage("Registration failed");
                    console.error("Registration failed:", error);
                });
        }
    };

    return (
        <div className="register-form">
            <h2>Register</h2>
            <form onSubmit={handleRegisterSubmit}>
                <div>
                    <input
                        type="text"
                        name="firstName"
                        placeholder="First Name"
                        value={registerForm.firstName}
                        onChange={handleRegisterChange}
                    />
                    {errors.firstName && <div className="error">{errors.firstName}</div>}
                </div>
                <div>
                    <input
                        type="text"
                        name="lastName"
                        placeholder="Last Name"
                        value={registerForm.lastName}
                        onChange={handleRegisterChange}
                    />
                    {errors.lastName && <div className="error">{errors.lastName}</div>}
                </div>
                <div>
                    <input
                        type="email"
                        name="email"
                        placeholder="Email"
                        value={registerForm.email}
                        onChange={handleRegisterChange}
                    />
                    {errors.email && <div className="error">{errors.email}</div>}
                </div>
                <div>
                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        value={registerForm.password}
                        onChange={handleRegisterChange}
                    />
                    {errors.password && <div className="error">{errors.password}</div>}
                </div>
                <button type="submit">Register</button>
                <Link to="/" >
                    Go back
                </Link>
            </form>
            <p>{message}</p>
        </div>
    );
}

export default RegisterForm;
