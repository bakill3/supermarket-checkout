# Supermarket Checkout System

This project is a simplified supermarket checkout system implemented in PHP. It calculates the total price of scanned items, applying special pricing rules where applicable.

## Features

- **Scan Items:** Identify items by SKUs (A, B, C, etc.)
- **Promotions:**
  - Multipriced promotions (e.g., 2 for £1.25)
  - Buy-n-get-1-free promotions
  - Meal deal promotions (e.g., buy D and E for £3)

## Setup

### Requirements

- PHP 7.4+

### Installation

1. **Clone the repository:**
   ```
   git clone https://github.com/bakill3/supermarket-checkout.git
   ```
2. Navigate to the project directory:
	```
	cd supermarket-checkout
	```
### Usage
1. Define your items and special prices in checkout_system.php.
2. Run the example usage script:
	```
	php usage_example.php
	```

### Example Output
  ```
	Total price: 7.00 €
	```
  
## Project Structure
- checkout_system.php: Contains the class definitions for the Item, SpecialPrice, and Checkout classes.
- usage_example.php: Contains the example usage script to demonstrate how the checkout system works.
