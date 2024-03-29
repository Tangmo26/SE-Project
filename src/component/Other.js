import React, { useState, useEffect } from "react";
import axios from "axios";
import "../Home.css";
import ShoppingCart from "./Shopping_cart";

export default function Other() {
    const [products, setProducts] = useState([]);
    const shoppingCart = new ShoppingCart();
    const path = 'other';

    useEffect(() => {
        axios
            .get(`http://localhost/php-react/Login-and-Register/Product.php/${path}`)
            .then((response) => {
                setProducts(response.data);
            })
            .catch((error) => {
                console.log(error);
            });
    }, []);

    const handleAddToCart = (product) => {
        shoppingCart.addToCart(product);
    };

    return (
        <div className="container-home">
            {products.length > 0 && (
                <div className="card">
                    {products.map((product) => (
                        <div key={product.Product_ID}>
                            <img src={`./Upload/`+product.Product_Image} alt={product.Product_Name} />
                            <div>
                                <h5>{product.Product_Name}</h5>
                                <p>{product.Product_Detail}</p>
                                <h6>{product.Product_Price}</h6>
                                <button onClick={() => handleAddToCart(product)}>
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}
            {products.length === 0 && <p className="text-empty">Product is empty</p>}
        </div>
    );
}
