# Content Approval REST API Documentation

This API supports full REST operations for managing posts in the Content Approval application. It uses **Laravel Passport** for secure, token-based authentication.

---

## Base URL
All requests should be prefixed to your application Domain:
`http://content_approval.test/api/`

## Headers
All authenticated endpoints require the following headers:
```http
Accept: application/json
Authorization: Bearer <your_access_token>
```

---

## 1. Authentication

### Login and Get Token
Use this endpoint to exchange email/password credentials for an access token.

- **URL:** `/api/login`
- **Method:** `POST`
- **Authentication Required:** No

**Request Body:**
```json
{
  "email": "author@example.com",
  "password": "password"
}
```

**Success Response (200 OK):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Author User",
    "email": "author@example.com",
    "role": "author"
  },
  "access_token": "eyJ0eXAiOiJKV1QiLCJ..."
}
```

**Error Responses:**
- `401 Unauthorized`: Invalid login credentials.
- `422 Unprocessable Entity`: Missing `email` or `password` parameters.

---

## 2. Posts (CRUD Operations)

### List Posts
Retrieve a paginated or array list of posts. 
- Authors see *only* their own posts. 
- Managers and Admins see *all* posts inside the system.

- **URL:** `/api/posts`
- **Method:** `GET`
- **Authentication Required:** Yes (Any Role)

**Success Response (200 OK):**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "title": "My New Ideas",
    "body": "This is a detailed article outlining ideas...",
    "status": "pending",
    "approved_by": null,
    "rejected_reason": null,
    "created_at": "2026-02-28T16:20:00.000000Z",
    "updated_at": "2026-02-28T16:20:00.000000Z"
  }
]
```

### Create a Post
Create a new submission for approval. The system automatically tags it to the authenticated Author and sets the status to `pending`.

- **URL:** `/api/posts`
- **Method:** `POST`
- **Authentication Required:** Yes (**Authors Only**)

**Request Body:**
```json
{
  "title": "A Great Post",
  "body": "The main content text here."
}
```

**Success Response (201 Created):**
Returns the created post object.

**Error Responses:**
- `403 Forbidden`: `{"message": "Unauthorized. Only authors can create posts."}`

### Get a Specific Post
Retrieves full details of a post, including the activity logs and the Author relationship.

- **URL:** `/api/posts/{id}`
- **Method:** `GET`
- **Authentication Required:** Yes (Authors can only view their own IDs)

**Success Response (200 OK):**
Returns the Post object with loaded `user`, `approvedBy`, and `logs` arrays.

### Update a Post
Updates the title or body. If the post was rejected or approved, making an update automatically reverts its status back to `pending`.

- **URL:** `/api/posts/{id}`
- **Method:** `PUT` (or `PATCH`)
- **Authentication Required:** Yes (**Authors Only, and only for their own posts**)

**Request Body:**
```json
{
  "title": "Updated Title",
  "body": "Updated specific text here."
}
```

**Success Response (200 OK):**
Returns the updated post object.

### Delete a Post
Removes the post from the system (Soft Delete). An activity log of the deletion is preserved.

- **URL:** `/api/posts/{id}`
- **Method:** `DELETE`
- **Authentication Required:** Yes (**Admins Only**)

**Success Response (200 OK):**
```json
{
  "message": "Post deleted successfully."
}
```

**Error Responses:**
- `403 Forbidden`: `{"message": "Unauthorized. Only admins can delete."}`

---

## 3. Manager/Admin Workflow

### Approve a Post
Marks a pending post as `approved`. Logs the action timestamp and the manager's ID.

- **URL:** `/api/posts/{id}/approve`
- **Method:** `POST`
- **Authentication Required:** Yes (**Managers or Admins Only**)

**Success Response (200 OK):**
Returns the updated post object with `"status": "approved"` and populated `approved_by` ID.

### Reject a Post
Marks a post as `rejected` and appends a reason for the Author to view.

- **URL:** `/api/posts/{id}/reject`
- **Method:** `POST`
- **Authentication Required:** Yes (**Managers or Admins Only**)

**Request Body:**
```json
{
  "rejected_reason": "The tone of this article does not fit our brand guidelines. Please revise."
}
```

**Success Response (200 OK):**
Returns the updated post object with `"status": "rejected"` and the `"rejected_reason"` populated.

**Error Responses:**
- `422 Unprocessable Entity`: The `rejected_reason` string was left blank or null.

---

## 4. Activity Logs
Activity logging happens automatically in the background on every request. Any `GET` request to a post (`/api/posts` or `/api/posts/{id}`) will include a nested `logs` array:

```json
"logs": [
  {
    "id": 1,
    "post_id": 1,
    "user_id": 1,
    "action": "created",
    "created_at": "2026-02-28T16:20:00.000000Z"
  },
  {
    "id": 2,
    "post_id": 1,
    "user_id": 2,
    "action": "approved",
    "created_at": "2026-02-28T16:25:00.000000Z"
  }
]
```
