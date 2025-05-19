# Backend API

**Prerequisites:**
- Docker and Docker Compose must be installed.

---

## Running the Application

To start the application and all required services (API, database, etc.):

```bash
./start.sh
```

- The API will be available at: [http://localhost:8080](http://localhost:8080)
- You can access the API endpoints as described in the requirements (e.g., `/api/wagers`).

---

### Code Style

To check coding standards:
```bash
docker compose exec app composer run cs-check
```

To automatically fix coding standards:
```bash
docker compose exec app composer run cs-fix
```

---

### Running Tests

To run the full test suite (feature and unit tests):

```bash
./start-test.sh
```

Or, to run tests for a specific feature (e.g., wagers):

```bash
docker compose exec app php artisan test --filter=List
```

**Prerequisites:**
- All containers should be up (use `docker compose up -d` if needed).

**Expected output:**
- All tests should pass with no errors or warnings.
- You should see a summary of passed/failed tests in the console.
