import React, { useState, useEffect } from "react";
import {useParams, useNavigate, Link, Navigate} from "react-router-dom";
import api from "../api";
import "../../css/update.css";
import {useAuth} from "../../useAuth";
import Swal from "sweetalert2";


function UpdateBook() {
    const { id } = useParams();
    const navigate = useNavigate();
    const isAuthenticated = useAuth();
    const [book, setBook] = useState({
        isbn: "",
        title: "",
        description: "",
        author: "",
        publicationDate: "",
    });

    useEffect(() => {
        api
            .get(`/books/${id}`)
            .then((response) => {
                setBook(response.data);
            })
            .catch((error) => {
                console.error("Error fetching book:", error);
            });
    }, [id]);

    const handleUpdate = () => {
        api
            .put(`/books/${id}`, book)
            .then(() => {
                Swal.fire('Book updated successfully!','',"success");
                navigate("/showBooks");
            })
            .catch((error) => {
                console.error("Error updating book:", error);
            });
    };
    return (
        <div>
            <h2 className="title1">Update Book</h2>
            <div>
                <label>ISBN:</label>
                <input
                    type="text1"
                    value={book.isbn}
                    onChange={(e) => setBook({ ...book, isbn: e.target.value })}
                />
            </div>
            <div>
                <label>Title:</label>
                <input
                    type="text1"
                    value={book.title}
                    onChange={(e) => setBook({ ...book, title: e.target.value })}
                />
            </div>
            <div>
                <label>Description:</label>
                <input
                    type="text1"
                    value={book.description}
                    onChange={(e) => setBook({ ...book, description: e.target.value })}
                />
            </div>
            <div>
                <label>Author:</label>
                <input
                    type="text1"
                    value={book.author}
                    onChange={(e) => setBook({ ...book, author: e.target.value })}
                />
            </div>
            <div>
                <label>Publication Date:</label>
                <input
                    type="text1"
                    value={book.publicationDate}
                    onChange={(e) =>
                        setBook({ ...book, publicationDate: e.target.value })
                    }
                />
            </div>
            <button type="button1" className={"button"} onClick={handleUpdate}>Update</button>
            <Link to="/showBooks">Cancel</Link>
        </div>
    );
}

export default UpdateBook;