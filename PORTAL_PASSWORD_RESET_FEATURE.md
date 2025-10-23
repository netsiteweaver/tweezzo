# Portal Password Reset Feature

## Overview
This feature allows administrators to reset customer portal access passwords directly from the customer listing page. The newly generated password is automatically emailed to the user and displayed to the administrator for manual sharing via WhatsApp or other channels.

## Features Implemented

### 1. Customer Listing Enhancement
- Added a new **key icon button** (🔑) in the Actions column of the customer listing
- Button is only visible to users with edit permissions

### 2. Portal Access Management Modal
When clicking the key icon button, a modal displays:
- Customer company name
- List of all users with portal access to that customer account
- User details: Name, Email, Phone, Job Description, Admin status
- Individual "Reset Password" button for each user

### 3. Password Reset Functionality
When resetting a user's password:
1. Generates a secure 12-character random password using `genPassword()` function
2. Updates the password in the `customer_access` table (MD5 hashed)
3. Sends an automated email to the user with:
   - Their new password
   - Customer portal login link
   - Professional email template
4. Displays the password to the administrator in a success modal

### 4. Password Display Modal
After successful password reset:
- Shows user's name and email
- Displays the new password in a copyable field
- **Copy to Clipboard** button for easy copying
- Confirms that email has been sent
- Allows administrator to copy and send password via WhatsApp

## Files Modified/Created

### Modified Files:
1. **application/views/customers/listing.php**
   - Added password management button
   - Added two new modals: `modalPortalPassword` and `modalNewPassword`

2. **application/controllers/Customers.php**
   - Added `get_portal_access_users()` method - retrieves all portal users for a customer
   - Added `reset_portal_password()` method - handles password reset and email sending

3. **assets/js/pages/customers.js**
   - Added click handler for password management button
   - Added dynamic user list loading
   - Added password reset confirmation and processing
   - Added clipboard copy functionality

### New Files:
1. **application/views/_email/portalPasswordReset.php**
   - Professional email template for password reset notifications
   - Includes user name, company name, new password, and login link

## Usage Instructions

### For Administrators:

1. **Navigate to Customer Listing**
   - Go to Customers > Listing

2. **Access Portal Password Management**
   - Find the customer whose portal access you want to manage
   - Click the blue **key icon** (🔑) button in the Actions column

3. **View Portal Users**
   - A modal will appear showing all users with access to this customer's portal
   - Review the list of users with their details

4. **Reset a User's Password**
   - Click the "Reset Password" button next to the desired user
   - Confirm the action in the confirmation dialog
   - Wait for the password to be reset

5. **Copy and Share Password**
   - After successful reset, a new modal appears with the generated password
   - Click "Copy" button to copy password to clipboard
   - Share the password with the client via WhatsApp or other channels
   - The user will also receive an email with their new password

## Security Features

- Only users with edit permissions can reset passwords
- Password reset requires confirmation
- Passwords are 12 characters long and randomly generated
- Passwords are stored as MD5 hashes in the database
- Email notifications are automatically sent to users
- Audit trail maintained through database updates

## Database Tables Used

- **customers** - Customer information
- **customer_access** - Portal access user credentials
- **email_queue** - Email notifications queue

## API Endpoints

1. **POST /customers/get_portal_access_users**
   - Parameters: `uuid` (customer UUID)
   - Returns: List of portal access users for the customer

2. **POST /customers/reset_portal_password**
   - Parameters: `access_id` (customer_access ID)
   - Returns: New password and success confirmation

## Email Notification

The password reset email includes:
- Personalized greeting with user's name
- Customer company name
- New password in a highlighted box
- Direct link to customer portal signin page
- Professional formatting with company logo

## Technical Notes

- Uses jQuery for AJAX calls and DOM manipulation
- Uses Bootbox for confirmation dialogs
- Uses Alertify for success/error notifications
- Follows existing codebase patterns and conventions
- Compatible with the current authentication system (MD5 hashing)

## Future Enhancements (Optional)

- Add password strength requirements
- Implement password expiration
- Add 2-factor authentication
- Allow users to reset their own passwords
- Add activity log for password resets
- Implement more secure password hashing (bcrypt instead of MD5)

---

**Implementation Date:** October 23, 2025
**Status:** ✅ Complete and Ready for Use

