import React, { useState, useEffect } from 'react';
import {Link, Navigate, useNavigate, useParams} from 'react-router-dom';
import api from '../api';
import {useAuth} from "../../useAuth";
import Swal from "sweetalert2";

function AddReview() {
    const navigate = useNavigate();
    const isAuthenticated = useAuth();
    const { id } = useParams();
    const [book, setBook] = useState(null);
    const [reviewData, setReviewData] = useState({
        book: null,
        author: '',
        body: '',
        rating: 0,
        publicationDate: '',
    });

    useEffect(() => {
        api.get(`/books/${id}`)
            .then((response) => {
                const book = response.data;
                setBook(book);

                setReviewData({
                    ...reviewData,
                    book: `/books/${book.id}`,
                });
            })
            .catch((error) => {
                console.error('Error fetching book:', error);
            });
    }, [id]);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        const intValue = name === 'rating' ? parseInt(value, 10) : value;

        setReviewData({
            ...reviewData,
            [name]: intValue,
        });
    };

    const handleFormSubmit = (e) => {
        e.preventDefault();

        api.post('/reviews', reviewData)
            .then(() => {
                console.log('Review added successfully');
                Swal.fire('Review added successfully!','',"success");
                navigate('/showBooks');
            })
            .catch((error) => {
                console.error('Error adding review:', error);
            });
    };

    return (
        <div className="add-review">
            <h2 className="title1">Add Review</h2>
            <form onSubmit={handleFormSubmit}>
                <div>
                    <input
                        type="text"
                        name="author"
                        placeholder="Author"
                        value={reviewData.author}
                        onChange={handleInputChange}
                    />
                </div>
                <div>
                    <input
                        type="text"
                        name="body"
                        placeholder="Body"
                        value={reviewData.body}
                        onChange={handleInputChange}
                    />
                </div>
                <div>
                    <input
                        type="number"
                        name="rating"
                        placeholder="Rating"
                        value={reviewData.rating}
                        onChange={handleInputChange}
                    />
                </div>
                <div>
                    <input
                        type="text"
                        name="publicationDate"
                        placeholder="Publication Date"
                        value={reviewData.publicationDate}
                        onChange={handleInputChange}
                    />
                </div>

                <button type="submit">Add Review</button>
                <Link to="/showBooks">Cancel</Link>
            </form>
        </div>
    );
}

export default AddReview;
