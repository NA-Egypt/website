# reCAPTCHA Integration Setup

This document explains the reCAPTCHA integration that has been implemented for the contact form.

## What's Been Implemented

1. **Google reCAPTCHA Package**: Installed `google/recaptcha` composer package
2. **Environment Configuration**: Added reCAPTCHA keys to `.env` file
3. **Frontend Integration**: Added reCAPTCHA widget to the contact form
4. **Backend Validation**: Updated ContactUsController to validate reCAPTCHA responses
5. **Error Handling**: Added proper error messages and form validation
6. **Tests**: Created tests to verify the implementation

## Files Modified

- `.env` - Added reCAPTCHA configuration
- `resources/views/frontend/contactus.blade.php` - Added reCAPTCHA widget and error handling
- `resources/views/components/frontend/layout.blade.php` - Added reCAPTCHA JavaScript
- `app/Http/Controllers/ContactUsController.php` - Added reCAPTCHA validation
- `tests/Feature/ContactUsRecaptchaTest.php` - Added tests

## Configuration Required

### 1. Get reCAPTCHA Secret Key

You have the site key: `6LeJlIoqAAAAAG2bJEdS6auOg2lROzXvIRkslM7_`

You need to:
1. Go to [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
2. Find your site configuration
3. Get the **Secret Key** (different from the Site Key)
4. Update the `.env` file:

```env
RECAPTCHA_SECRET_KEY=your_actual_secret_key_here
```

### 2. Clear Configuration Cache

After updating the `.env` file, run:

```bash
php artisan config:clear
php artisan cache:clear
```

## How It Works

1. **Frontend**: The reCAPTCHA widget appears on the contact form
2. **Validation**: When the form is submitted, both client-side and server-side validation occur
3. **Verification**: The server verifies the reCAPTCHA response with Google's servers
4. **Error Handling**: If verification fails, the user sees an error message
5. **Success**: If verification passes, the contact message is processed normally

## Testing

Run the tests to verify everything is working:

```bash
php artisan test tests/Feature/ContactUsRecaptchaTest.php
```

## Security Features

- Server-side validation prevents bypassing client-side checks
- IP address verification for additional security
- Proper error handling prevents information leakage
- Form data is preserved on validation errors for better UX

## Troubleshooting

1. **reCAPTCHA not showing**: Check that the JavaScript is loading and the site key is correct
2. **Validation always fails**: Verify the secret key is correct and the server can reach Google's API
3. **Form not submitting**: Check browser console for JavaScript errors

## Next Steps

1. Replace `YOUR_SECRET_KEY_HERE` in `.env` with your actual secret key
2. Test the form on your live site
3. Monitor for any issues in your application logs
