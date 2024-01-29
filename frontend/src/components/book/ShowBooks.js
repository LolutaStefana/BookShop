import React, { useEffect, useState } from "react";
import {Link, Navigate, useNavigate} from "react-router-dom";
import api from "../api";
import "../../css/show.css";
import LikeIcon from "../../LikeIcon";
import UnlikeIcon from "../../UnlikeIcon";
import {useAuth} from "../../useAuth";
import Swal from "sweetalert2";

function ShowBooks() {
    const navigate = useNavigate();
    const isAuthenticated = useAuth();
    const [books, setBooks] = useState([]);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 3;
    const userId = "/users/" + localStorage.getItem("userId");

    useEffect(() => {
        api
            .get(`/books?page=${currentPage}&itemsPerPage=${itemsPerPage}`)
            .then((response) => {
                console.log(response.data);
                const booksWithLikedStatus = response.data["hydra:member"].map(
                    (book) => ({
                        ...book,
                        liked: book.likingUsers?.includes(userId) || false,
                    })
                );
                setBooks(booksWithLikedStatus);
                setLoading(false);
            })
            .catch((error) => {
                console.error("Error fetching books:", error);
                setLoading(false);
            });
    }, [currentPage, itemsPerPage, userId]);


    if (loading) {
        return <div>Loading...</div>;
    }

    const handleDelete = (id) => {
        Swal.fire({
            title: 'Do you want to delete this book?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                api
                    .delete(`/books/${id}`)
                    .then(() => {
                        setBooks(books.filter((book) => book.id !== id));
                        Swal.fire('Deleted!', 'The book has been deleted.', 'success');
                    })
                    .catch((error) => {
                        console.error("Error deleting book:", error);
                        Swal.fire({
                            title: "Permission denied",
                            text: "You are not authorized to delete this book!",
                            icon: "error",
                        });
                    });
            }
        });
    };

    const handleUpdate = (id, ownerId) => {
        if (userId === ownerId) {
            navigate(`/updateBook/${id}`);
        } else {
            Swal.fire({
                title: "Permission denied",
                text: "You are not authorized to update this book!",
                icon: "error",
            });
        }
    };

    const handleAddReview = (id) => {
        navigate(`/addReview/${id}`);
    };

    const handleAdd = () => {
        navigate(`/addBook`);
    };

    const toggleLikedStatus = (id, liked) => {
        let requestMethod = "POST";
        if (liked) {
            requestMethod = "DELETE";
        }

        api({
            method: requestMethod,
            url: `/books/${id}/like`,
            data: {},
        })
            .then(() => {
                const updatedBooks = books.map((book) => {
                    if (book.id === id) {
                        return {
                            ...book,
                            liked: !liked,
                        };
                    }
                    return book;
                });
                setBooks(updatedBooks);
            })
            .catch((error) => {
                console.error("Error toggling liked status:", error);
            });
    };
    if (!isAuthenticated) {

        return <Navigate to="/" />;
    }

    return (
        <div>
            <h2 className="title1">List of books</h2>
            <button type="add-button" onClick={() => handleAdd()}>
                Add new book
            </button>
            <table className="book-table">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Description</th>
                    <th>Publication Date</th>
                    <th>Reviews</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                {books.map((book) => (
                    <tr key={book.id}>
                        <td>{book.title}</td>
                        <td>{book.author}</td>
                        <td>{book.description}</td>
                        <td>{book.publicationDate}</td>
                        <td>
                            <ul>
                                {book.reviews.map((review) => (
                                    <li key={review.id}>{review.body}</li>
                                ))}
                            </ul>
                        </td>
                        <td>
                            <button
                                type="action-button"
                                onClick={() => handleDelete(book.id)}
                            >
                                Delete
                            </button>
                            <button
                                type="action-button"
                                onClick={() => handleUpdate(book.id, book.owner)}
                            >
                                Update
                            </button>
                            <button
                                type="action-button"
                                onClick={() => handleAddReview(book.id)}
                            >
                                Add Review
                            </button>
                            {book.liked ? (
                                <button
                                    type="act-button"
                                    onClick={() => toggleLikedStatus(book.id, true)}
                                >
                                    <UnlikeIcon />
                                </button>
                            ) : (
                                <button
                                    type="act-button"
                                    onClick={() => toggleLikedStatus(book.id, false)}
                                >
                                    <LikeIcon />
                                </button>
                            )}
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
                    disabled={books.length === 0 || books.length < itemsPerPage}
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

export default ShowBooks;
