# Ticket Management App

A simple ticket management web app built with **Twig** and **Tailwind CSS**.
It includes pages for landing, login, signup, dashboard, and ticket management, with smooth toast-style user feedback.

---

## Features

* Beautiful UI built with **Tailwind CSS**
* Basic authentication flow (login/signup simulation)
* Multiple pages:

  * Landing Page
  * Login Page
  * Signup Page
  * Dashboard
  * Ticket Management
* User feedback via alert or toast-style messages
* Simple routing and templating using Twig

---

## Tech Stack

* **Twig** (PHP Templating Engine)
* **Tailwind CSS** (Utility-first CSS framework)
* **PHP** (Backend rendering)

---

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/Toluwaa-o/ticket_manager_twig.git
   cd ticket-manager-twig
   ```

2. Install dependencies using Composer:

   ```bash
   composer require twig/twig
   ```

3. Run the app using a local PHP server:

   ```bash
   php -S localhost:8000 -t public
   ```

4. Open the app in your browser at:

   ```
   http://localhost:8000
   ```

---

## How It Works

* Each page is rendered through a Twig template.
* Basic logic (like login/signup simulation) can be handled in PHP before rendering the Twig view.
* Tailwind handles all styling for a responsive and clean interface.
* Toast or alert-style feedback messages confirm user actions.

---

## Scripts

| Command                                                             | Description              |
| ------------------------------------------------------------------- | ------------------------ |
| `php -S localhost:8000 -t public`                                   | Start local PHP server   |
| `composer install`                                                  | Install PHP dependencies |

---

## Preview Flow

```
Landing → Login → Dashboard → Ticket Management
```