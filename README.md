
# multi-tenant-auth-app

## Requirements

- PHP >= 7.4
- MySQL
- Composer (for Firebase JWT package)

## Setup Instructions

1. Clone the repository:
    ```bash
    git clone https://github.com/zigglor/multi-tenant-auth-app
    ```

2. Install dependencies:
    ```bash
    cd multi-tenant-auth-app/auth
    composer require firebase/php-jwt
    ```

3. Configure the database connection in `auth/config.php`.

4. Run the project locally or deploy to your preferred host.

## API Endpoints

### `POST /auth/login.php`
- Accepts `email` and `password` via POST
- Returns `access_token`, sets `refresh_token` as a cookie

### `GET /auth/profile.php`
- Requires Bearer token in headers
- Returns user details

### `GET /auth/tenant.php`
- Requires Bearer token in headers
- Returns tenant details

## Notes

- Access tokens expire after 15 minutes.
- Refresh tokens last 7 days and are stored in secure HTTP-only cookies.
- Token includes `sub`, `tenant_id`, and `role` for each user.

## License

MIT License. See `LICENSE` for details.
