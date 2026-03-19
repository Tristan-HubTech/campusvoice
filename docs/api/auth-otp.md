# CampusVoice Auth OTP API Contract

## Overview

This document defines the OTP password-reset endpoints used by web and mobile clients.

Base API URL example:

- `http://localhost/campusvoice/public/api`

All request and response bodies are JSON.

## 1) Request Password OTP

- Method: `POST`
- URL: `/auth/password/otp/request`
- Auth: none

Request body:

```json
{
  "email": "student@example.com"
}
```

Success response (`201`):

```json
{
  "message": "OTP has been sent to your email. It will expire in 10 minutes."
}
```

Validation error (`400`):

```json
{
  "messages": {
    "email": "The email field must contain a valid email address."
  }
}
```

Rate-limit response (`429`):

```json
{
  "status": 429,
  "error": 429,
  "messages": {
    "error": "Please wait at least 60 seconds before requesting another OTP."
  }
}
```

## 2) Verify Password OTP

- Method: `POST`
- URL: `/auth/password/otp/verify`
- Auth: none

Request body:

```json
{
  "email": "student@example.com",
  "otp": "123456"
}
```

Success response (`200`):

```json
{
  "message": "OTP is valid."
}
```

Invalid OTP (`400`):

```json
{
  "messages": {
    "otp": "Invalid or expired OTP code."
  }
}
```

## 3) Reset Password

- Method: `POST`
- URL: `/auth/password/reset`
- Auth: none

Request body:

```json
{
  "email": "student@example.com",
  "otp": "123456",
  "new_password": "NewPass123!",
  "confirm_password": "NewPass123!"
}
```

Success response (`200`):

```json
{
  "message": "Password reset successful. Please login again."
}
```

Notes:

- On successful reset, all user API tokens are revoked.
- OTP expires after 10 minutes.
- OTP attempts are capped.

## Gmail SMTP Setup

In `.env`, set:

```ini
email.fromEmail = 'aclcmandaue8@gmail.com'
email.fromName = 'CampusVoice'
email.protocol = smtp
email.SMTPHost = 'smtp.gmail.com'
email.SMTPUser = 'aclcmandaue8@gmail.com'
email.SMTPPass = 'gqjn dmrd upls ooor'
email.SMTPPort = 587
email.SMTPCrypto = 'tls'
```

Use Google App Password, not your normal Gmail password.
