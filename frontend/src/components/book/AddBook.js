import React, { useState } from 'react';
import {Link, Navigate, useNavigate} from 'react-router-dom';
import api from '../api';
import {useAuth} from "../../useAuth";
import Swal from "sweetalert2";
function AddBook() {
    const navigate = useNavigate();
    const isAuthenticated = useAuth();
    const [bookData, setBookData] = useState({
        isbn: '',
        title: '',
        description: '',
        author: '',
        publicationDate: '',
    });

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setBookData({
            ...bookData,
            [name]: value,
        });
    };

    const handleFormSubmit = (e) => {
        e.preventDefault();
        api.post('/books', bookData)
            .then(() => {
                console.log('Book added successfully');
                Swal.fire('Book added successfully!','',"success");
                navigate('/showBooks');
            })
            .catch((error) => {
                console.error('Error adding book:', error);
            });
    };

    return (
        <div className="add-book">
            <h2 className="title1">Add Book</h2>
            <form onSubmit={handleFormSubmit}>
                <div>
                    <input
                        type="text"
                        name="isbn"
                        placeholder="ISBN"
                        value={bookData.isbn}
                        onChange={handleInputChange}
                    />
                </div>
                <div>
                    <input
                        type="text"
                        name="title"
                        placeholder="Title"
                        value={bookData.title}
                        onChange={handleInputChange}
                    />
                </div>
                <div>
                    <input
                        type="text"
                        name="description"
                        placeholder="Description"
                        value={bookData.description}
                        onChange={handleInputChange}
                    />
                </div>
                <div>
                    <input
                        type="text"
                        name="author"
                        placeholder="Author"
                        value={bookData.author}
                        onChange={handleInputChange}
                    />
                </div>
                <div>
                    <input
                        type="text"
                        name="publicationDate"
                        placeholder="Publication Date"
                        value={bookData.publicationDate}
                        onChange={handleInputChange}
                    />
                </div>
                <button type="submit">Add Book</button>
                <Link to="/showBooks">Cancel</Link>
            </form>
        </div>
    );
}

export default AddBook;
