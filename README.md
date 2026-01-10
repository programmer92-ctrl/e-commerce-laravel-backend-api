## This is a Laravel Backend API for an E-Commerce Website

## Documentation
Order

api/order/add

  send a json payload:
    {
    "order_items": [
        {
            "product_id": 8,
            "quantity": 1,
            "product_name": "Gucci Shorts",
            "product_price": 600,
            "subtotal": 600
        }
    ],
    "user_id": 1,
    "order_number": "3", <-- Needs to be unique 4,5,6,7
    "total_amount": 600.00,
    "subtotal_amount": 600.00,
    "shipping_cost": 13.00,
    "tax_amount": 6.00,
    "discount_amount": "0.00",
    "payment_method": "credit",
    "payment_status": "pending",
    "status": "pending",
    "currency": "USD",
    "shipping_method": "Ground",
    "tracking_number": "1",
    "notes": "No notes"
}

Product

api/products/add
name
slug
description
price
images[] <-- use brackets
