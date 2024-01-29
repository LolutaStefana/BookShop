import React, { useState, useEffect } from "react";
import {useParams, useNavigate, Link, Navigate} from "react-router-dom";
import api from "../api";
import "../../css/update.css";
import {useAuth} from "../../useAuth";
import Swal from "sweetalert2";
function UpdateReview() {
    const { id } = useParams();
    const isAuthenticated = useAuth();
    const navigate = useNavigate();
    const [review, setReview] = useState({
        author: "",
        body: "",
        rating: 0,
        publicationDate: "",
    });

    useEffect(() => {
        api
            .get(`/reviews/${id}`)
            .then((response) => {
                setReview(response.data);
            })
            .catch((error) => {
                console.error("Error fetching review:", error);
            });
    }, [id]);

    const handleUpdate = () => {
        api
            .put(`/reviews/${id}`, review)
            .then(() => {
                Swal.fire('Review updated successfully!','',"success");
                navigate("/showReviews");
            })
            .catch((error) => {
                console.error("Error updating review:", error);

            });
    };
    if (!isAuthenticated) {

        return <Navigate to="/" />;
    }

    return (
        <div>
            <h2 className="title1">Update Review</h2>
            <div>
                <label>Author:</label>
                <input
                    type="text1"
                    value={review.author}
                    onChange={(e) => setReview({ ...review, author: e.target.value })}
                />
            </div>
            <div>
                <label>Body:</label>
                <input
                    type="text1"
                    value={review.body}
                    onChange={(e) => setReview({ ...review, body: e.target.value })}
                />
            </div>
            <div>
                <label>Rating:</label>
                <input
                    type="text1"
                    value={review.rating}
                    onChange={(e) => setReview({ ...review, rating: e.target.value })}
                />
            </div>
            <div>
                <label>Publication Date:</label>
                <input
                    type="text1"
                    value={review.publicationDate}
                    onChange={(e) =>
                        setReview({ ...review, publicationDate: e.target.value })
                    }
                />
            </div>
            <button type="button1" className={"button"} onClick={handleUpdate}>
                Update
            </button>
            <Link to="/showReviews">Cancel</Link>
        </div>
    );
}

export default UpdateReview;