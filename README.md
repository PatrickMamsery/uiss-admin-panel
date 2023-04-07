##Installation guides

    1. clone the repository using the command
        `git clone https://github.com/PatrickMamsery/uiss-admin-panel.git`
    2. Navigate to the project's directory using the command `cd uiss-admin-panel`
    3. Run `composer update` to load dependencies
    4. Run `cp .env.example .env` to generate an environment file
    5. In the `.env` file put in appropriate credentials for DB_DATABASE, DB_USERNAME and DB_PASSWORD
    6. Create the corresponding database in your DBMS
    7. Run migrations using `php artisan migrate`
    8. Create an admin user for authentication purposes
        `php artisan orchid:admin Admin admin@admin.com password`
    9. Create new Orchid Link to properly serve JS and CSS files using the command
        `php artisan orchid:link`
    10. Run the application `php artisan serve`

UISS API Collection
Authorization
Bearer Token
Token
eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiOGQ4ODBiZDM1Nzk3MzE5ZGJjMzgwNGNjZTVhZjc5MmZmMGNkNTQzYmQ1Y2VlMGUyZjJkZTZlYmZiMTY2YzIwMTAxYzNlMTgyNDU3ZGRjMTUiLCJpYXQiOjE2Nzk0NDk2MDQuNTUwNzE5LCJuYmYiOjE2Nzk0NDk2MDQuNTUwNzQsImV4cCI6MTcxMTA3MjAwMy42MTc5OTIsInN1YiI6IjEiLCJzY29wZXMiOltdfQ.eJND6H6iLUMxprOOaMSjn3HmCGVL3boOEDjc8OAI1aEqjIhMpPBpiFYPp0I9HAthhkxIY2OSXduWJ3U92yZl7zkRGt4NfNSxi1GmxRUl110OjqeaTm4XhKx7zCgjnCwhEZNSNinruEJB7YTRnPgaTQykBaCOg84cOQwCe4zJGWB57nV1nJOy_He5dBZL9TNV_iAd_3Ib0O36AaNxQcn8pDjkbYVkCFORO7N5_S3X2ALzl6KPYEYaHAbwmHDpydhXNfjnuX033lV2YuqwwQ311Un8mTMXH6gHeHGbHzddCDohcyzi763cPXOvFLCN2XQtlNXzDqOLzaleQpnIbSAnKrm7A4OG0x2kXieBQy5XpEw8jkJP2AitBer_KG1vjtBipiiF3SuybBYSukmHrRz0BgyXlp2E9l6uXBn1wq3JHJSgeSApQCm0UxhSJwZjJJ_SAgHD8UCv_GgTARf4rAqxsFhTgCLLDKbIN_v3bWcsZbf78FSo2SUwyCh3SFJW33qRBRAHeh-kvsdsCN36e3bW-S39Fj62c4BNDlzNLqXbFvI4M8fAtkvuy2Na1iRz4xF26RXJpWN-dRv3PQgQsA_5NRhueOuJWZwuwmtiktnWgtMY25iQHIzQwswog8aJG-dhFrCrICPTRSIpdfDkPv45znod8Aj3aKnLRGrgAfLiENfQ

Requests
Register (POST)
URL: https://admin.uiss.patrickmamsery.works/api/register
Description: Add request description...
Body:

