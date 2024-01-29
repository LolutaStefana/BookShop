import React, {useEffect} from "react";
import RegisterForm from "./components/RegisterForm";
import LoginForm from "./components/LoginForm";
import {BrowserRouter, Routes, Route} from "react-router-dom";
import SuccessPage from "./components/SuccessPage";
import ShowBooks from "./components/book/ShowBooks";
import AddBook from "./components/book/AddBook";
import ShowReviews from "./components/review/ShowReviews";
import ShowOrganisations from "./components/organisation/ShowOrganisations";
import "./App.css";
import UpdateBook from "./components/book/UpdateBook";
import UpdateReview from "./components/review/UpdateReview";
import AddReview from "./components/review/AddReview";
import AddOrganisation from "./components/organisation/AddOrganisation";
import UpdateOrganisation from "./components/organisation/UpdateOrganisation";


export default function App() {


    return (

    <BrowserRouter>
        <Routes>
            <Route path="/" element={<LoginForm />}>
            </Route>
            <Route path="/register" element={<RegisterForm />}>
            </Route>
            <Route  path="/success" element={<SuccessPage />}>
                </Route>
            <Route path="/showBooks" element={<ShowBooks />} >  </Route>
            <Route path="/showReviews" element={<ShowReviews/>} >  </Route>
            <Route path="/showOrganizations" element={<ShowOrganisations/>} >  </Route>
            <Route path="/addBook" element={<AddBook/>} >  </Route>
            <Route path="/updateBook/:id" element={<UpdateBook />} ></Route>
            <Route path="/updateReview/:id" element={<UpdateReview/>} ></Route>
            <Route path="/addReview/:id" element={<AddReview/>} >  </Route>
            <Route path="/addOrganization" element={<AddOrganisation/>} >  </Route>
            <Route path="/updateOrganisation/:id" element={<UpdateOrganisation/>} ></Route>


        </Routes>
    </BrowserRouter>
    );
}