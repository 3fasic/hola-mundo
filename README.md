# 📝 TaskManager - PHP Task Management Web Application

A modern, responsive task management web application built with PHP, MySQL, Bootstrap, and jQuery. Organize your tasks efficiently with a clean and intuitive interface.

## ✨ Features

### 🔐 User Authentication
- **Secure Registration & Login**: User registration with email validation and secure password hashing
- **Session Management**: Secure session handling with automatic logout
- **User Security**: Password validation, input sanitization, and SQL injection prevention

### 📋 Task Management
- **CRUD Operations**: Create, read, update, and delete tasks
- **Task Properties**:
  - Title and detailed description
  - Priority levels (Low, Medium, High)
  - Status tracking (Pending, In Progress, Completed)
  - Categories for organization
  - Due dates with visual indicators
- **Quick Actions**: Fast status updates and task completion
- **Bulk Operations**: Delete multiple tasks

### 🎨 Modern UI/UX
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile
- **Bootstrap 5**: Modern UI components with custom styling
- **Interactive Elements**: Hover effects, animations, and smooth transitions
- **Dark Mode Ready**: Theme system for future dark mode implementation
- **Accessibility**: Semantic HTML and keyboard navigation support

### 🔍 Advanced Features
- **Smart Filtering**: Filter by status, category, and search terms
- **Live Search**: Real-time search with debouncing
- **Statistics Dashboard**: Visual overview of task completion
- **Due Date Alerts**: Visual warnings for overdue and upcoming tasks
- **Draft Auto-save**: Automatic form draft saving to localStorage
- **Keyboard Shortcuts**: Quick navigation with keyboard shortcuts

### 🚀 Technical Features
- **AJAX Integration**: Seamless updates without page reloads
- **RESTful API**: JSON API for task operations
- **Progressive Enhancement**: Works with and without JavaScript
- **Security**: CSRF protection, input validation, and secure coding practices
- **Performance**: Optimized queries and efficient caching

## 🛠️ Installation & Setup

### Prerequisites
- **Web Server**: Apache or Nginx
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher (or MariaDB 10.2+)
- **Extensions**: PDO, MySQL PDO driver

### Quick Setup

1. **Clone or Download** the project files to your web server directory:
   ```bash
   git clone <repository-url> taskmanager
   cd taskmanager
   ```

2. **Configure Database** in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'task_manager');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

3. **Set Permissions** (Linux/Mac):
   ```bash
   chmod 755 -R .
   chmod 644 -R assets/
   ```

4. **Access the Application**:
   - Open your web browser
   - Navigate to `http://your-domain/taskmanager/`
   - The database will be created automatically on first access

### Database Setup (Automatic)

The application automatically creates the necessary database and tables on first run. No manual SQL execution required!

**Tables Created:**
- `users` - User accounts and authentication
- `tasks` - Task data with relationships

## 📖 Usage Guide

### Getting Started

1. **Register**: Create a new account with username, email, and password
2. **Login**: Sign in with your credentials
3. **Dashboard**: View your task overview and statistics
4. **Create Tasks**: Add new tasks with all necessary details
5. **Manage**: Edit, update status, or delete tasks as needed

### Task Management

#### Creating Tasks
1. Click "Add New Task" from dashboard or navigation
2. Fill in task details:
   - **Title**: Clear, actionable task name
   - **Description**: Detailed task information
   - **Priority**: Low, Medium, or High
   - **Category**: Organize tasks by type
   - **Due Date**: Optional deadline
3. Click "Create Task"

#### Managing Tasks
- **Quick Status Update**: Use action buttons on task cards
- **Detailed Edit**: Click edit button for full task editing
- **Filtering**: Use filters to find specific tasks
- **Search**: Type in search box for instant results
- **Delete**: Remove tasks with confirmation

### Navigation

#### Keyboard Shortcuts
- `Ctrl/Cmd + N`: Create new task
- `Ctrl/Cmd + /`: Focus search box
- `Escape`: Close dropdowns/modals

#### Features
- **Dashboard**: Overview and statistics
- **All Tasks**: Complete task listing with filters
- **Add Task**: Quick task creation
- **User Menu**: Account options and logout

## 🏗️ Technical Architecture

### File Structure
```
taskmanager/
├── index.php              # Main entry point and routing
├── config/
│   └── database.php       # Database configuration
├── includes/
│   ├── functions.php      # Core functionality
│   ├── header.php         # HTML header and navigation
│   └── footer.php         # HTML footer and scripts
├── pages/
│   ├── dashboard.php      # Dashboard with statistics
│   ├── login.php          # User login form
│   ├── register.php       # User registration form
│   ├── logout.php         # Logout handler
│   ├── tasks.php          # Task listing and management
│   ├── add-task.php       # Task creation form
│   └── edit-task.php      # Task editing form
├── api/
│   └── tasks.php          # RESTful API endpoints
├── assets/
│   ├── css/
│   │   └── style.css      # Custom styles
│   └── js/
│       └── app.js         # JavaScript functionality
└── README.md              # This file
```

### Technology Stack
- **Backend**: PHP 7.4+ with PDO
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **UI Framework**: Bootstrap 5.3
- **Icons**: Font Awesome 6.4
- **JavaScript Library**: jQuery 3.7

### Security Measures
- **Password Hashing**: PHP `password_hash()` with strong algorithms
- **Input Sanitization**: All user input sanitized and validated
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Protection**: HTML entity encoding
- **Session Security**: Secure session configuration
- **CSRF Protection**: Form tokens and validation

## 🔧 Customization

### Styling
- Edit `assets/css/style.css` for custom styles
- Modify CSS variables in `:root` for color schemes
- Add custom classes for specific styling needs

### Functionality
- Extend `includes/functions.php` for additional features
- Add new pages in `pages/` directory
- Enhance API in `api/tasks.php` for more endpoints

### Database
- Modify schema in `config/database.php`
- Add new fields to existing tables
- Create additional tables for extended features

## 🐛 Troubleshooting

### Common Issues

**Database Connection Error**
- Check database credentials in `config/database.php`
- Ensure MySQL service is running
- Verify user has appropriate permissions

**Permission Denied**
- Set correct file permissions (755 for directories, 644 for files)
- Ensure web server has read access to all files

**JavaScript Not Working**
- Check browser console for errors
- Ensure jQuery and Bootstrap are loading
- Verify custom JavaScript syntax

**Styling Issues**
- Clear browser cache
- Check CSS file is loading correctly
- Verify Bootstrap CSS is included

## 🔮 Future Enhancements

### Planned Features
- **Dark Mode**: Toggle between light and dark themes
- **File Attachments**: Attach files to tasks
- **Team Collaboration**: Share tasks with other users
- **Task Templates**: Reusable task templates
- **Calendar Integration**: Calendar view of tasks
- **Mobile App**: Native mobile applications
- **Notifications**: Email and push notifications
- **Task Dependencies**: Link related tasks
- **Time Tracking**: Track time spent on tasks
- **Reporting**: Advanced analytics and reports

### Technical Improvements
- **API Documentation**: Swagger/OpenAPI documentation
- **Unit Testing**: PHPUnit test suite
- **Performance**: Redis caching and optimization
- **Security**: Two-factor authentication
- **Deployment**: Docker containerization
- **CI/CD**: Automated testing and deployment

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🤝 Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues for bugs and feature requests.

### Development Setup
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📞 Support

For support, bug reports, or feature requests:
- Open an issue on GitHub
- Check existing documentation
- Review troubleshooting section

---

**Built with ❤️ using PHP, MySQL, and modern web technologies.**
