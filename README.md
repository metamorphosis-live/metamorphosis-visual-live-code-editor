# Metamorphosis: Visual Live Code Editor 🚀

A professional web-based live code editor with real-time preview, designed for web developers and content managers. This editor enables direct editing of PHP, HTML, CSS, and JavaScript files with immediate visual feedback.

![Metamorphosis Visual Code Editor](https://metamorphosis.live/images/visual-code-editor.jpg)

## ✨ Key Features

### 🔐 Secure Authentication
- Password-protected access
- Session-based authentication
- Automatic logout on inactivity

### 📁 File Management
- **File Browser**: Overview of all files in the directory
- **Create New Files**: With automatic PHP extension
- **Delete Files**: With security confirmation
- **File Navigation**: Quick switching between files

### ✏️ Intelligent Code Editor
- **Ace Editor Integration** with syntax highlighting for:
  - PHP
  - HTML
  - CSS
  - JavaScript
- **Autocompletion** for all supported languages
- **Search & Replace** with hotkey support (Ctrl+F)
- **Undo/Redo** with position saving
- **Line numbers** and code folding
- **Multiple Layouts**: Horizontal/Vertical split

### 🔄 Real-time Preview
- **Live Edit Mode**: Changes appear immediately in preview window
- **Iframe Preview**: Isolated display of edited files
- **Cache Busting**: Automatic reload on changes

### 🎯 Element Selection (Click-to-Code)
- **Clickable Elements**: Click on elements in the preview window
- **Automatic Navigation**: Jumps to corresponding code line
- **Smart Detection**: Finds elements based on:
  - IDs
  - Classes
  - Text content
  - HTML attributes
- **Visual Highlighting**: Highlighted elements in preview

### 🛠️ Advanced Features
- **Split-Screen Layout**: Adjustable split between code and preview
- **Responsive Design**: Optimized for desktop and mobile
- **Hotkeys** for common actions:
  - `Ctrl+S`: Save
  - `Ctrl+F`: Search
  - `Ctrl+N`: New file
  - `Ctrl+Shift+D`: Delete file
  - `Ctrl+Shift+C`: Element selection mode
  - `Ctrl+Shift+L`: Toggle live edit
- **Session Storage**: Maintains editor state across page changes
- **LocalStorage**: Stores layout settings locally

### 📱 Responsive Interface
- **Collapsible Sidebar**: Expandable file list
- **Mobile Optimization**: Automatic layout adaptation
- **Touch Support**: Splitter works on touch devices

## 🚀 Quick Start

### Prerequisites
- PHP 7.4 or higher
- Web server (Apache, Nginx, etc.)
- Modern browser (Chrome, Firefox, Safari, Edge)

### Installation
1. **Clone repository or download file**
   https://github.com//metamorphosis-live/metamorphosis-visual-live-code-editor.git

2. **Upload to server**
- Upload `editor.php` to desired directory
- Ensure PHP is enabled on server

3. **Adjust credentials** (Optional)
- $user_admin = "admin";
- $pass_admin = "admin01ß#!2bAq";

5. **Access**
Open https://your-domain.com/path/editor.php
Log in with default credentials

## 🧩 Technologies

### Backend
- PHP 7.4+: Server-side logic and file management
- Session Management: Secure user authentication
- File System API: File operations (Read, Write, Delete)

### Frontend
- HTML5/CSS3: Modern, responsive interface
- JavaScript (ES6+): Dynamic interactions
- Ace Editor: Professional code editor
- Font Awesome: Icons for better UX

### Browser APIs
- LocalStorage: Persistent settings
- Iframe API: Isolated preview
- Fetch API: AJAX communication

## 🔧 Configuration

### Login credentials
- $user_admin = "admin";              // Username
- $pass_admin = "secret";             // Password (change!)
- $root_dir = __DIR__;                // Working directory

### Protected files
- $files = str_replace('.htaccess', '', $files); // Cannot be deleted or opened
- $files = str_replace('editor.php', '', $files); // Cannot be deleted or opened

### Security Settings
- Password Protection: Authorized users only
- File Protection: editor.php and .htaccess cannot be deleted
- Session Timeout: Automatic logout
- Input Validation: Protection against path traversal

## 📖 Usage

### Basic Operations
- Login: Enter username and password
- Select File: Click on filename in sidebar
- Edit Code: Use Ace Editor for changes
- Save: Ctrl+S or click Save button
- View Preview: See results on the right

### Advanced Features
- Element Selection (Click-to-Code)
- Activate element selection button (mouse pointer icon)
- Click on element in preview window
- Editor automatically jumps to corresponding code line
- Element is highlighted in preview

### Live Edit Mode
- Activate live edit button (lightning icon)
- Modify code in editor
- Preview updates automatically (500ms delay)

### Layout Customization
- Drag Splitter: Adjust code/preview area sizes
- Toggle Layout: Switch between horizontal and vertical arrangement
- Collapse Sidebar: Hide file list when needed

## 🛡️ Security Notes

### Critical Settings
- Change Password: Modify $pass_admin variable in code
- Enforce HTTPS: Via .htaccess or server configuration
- IP Restrictions: Allow only specific IPs
- Directory Permissions: Write permissions only where needed


## ©️ Credits

- Coded by [Metamorphosis](https://metamorphosis.live/) (Enrico Schulze - Web Developer - Webdesigner - SEO - Marketing)
