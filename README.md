# FlexFusion - Gym Management System

<div align="center">
  <img src="https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Bootstrap-5.3.2-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
  <img src="https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
</div>

## 🏋️‍♂️ Project Overview

FlexFusion is a comprehensive gym management system that connects gym administrators, trainers, and members in a seamless ecosystem. Built with modern web technologies, it provides a robust and user-friendly experience for managing all aspects of gym operations.

### Key Features

- **Multi-Role System**: Separate interfaces for Admin, Trainer, and Member
- **Comprehensive Management**: Member, trainer, workout, diet, and subscription management
- **Progress Tracking**: Real-time workout and diet progress monitoring
- **Communication System**: Built-in chat and notification system
- **AI Integration**: Smart workout analysis and personalized recommendations
- **Responsive Design**: Modern UI/UX that works on all devices

## 🚀 Features

### 👨‍💼 Admin Panel
- **Member Management**: Add, edit, and manage member profiles
- **Trainer Management**: Assign and monitor trainer performance
- **Workout & Diet Management**: Create and assign personalized plans
- **Subscription Management**: Handle payments and renewals
- **Reports & Analytics**: Comprehensive reporting and insights
- **Attendance Tracking**: Monitor member gym visits

### 🏋️‍♀️ Trainer Panel
- **Member Management**: View and manage assigned members
- **Workout Management**: Create custom workout plans
- **Progress Tracking**: Monitor member fitness goals
- **Communication**: Chat with members and provide guidance
- **Attendance Management**: Track member attendance

### 👤 Member Panel
- **Workout Management**: Access assigned workout plans
- **Diet Management**: View and track nutrition plans
- **Progress Tracking**: Record measurements and track goals
- **Attendance**: Check in/out and view history
- **Communication**: Chat with trainers and receive feedback
- **AI Coach**: Get personalized fitness guidance

## 🛠️ Technology Stack

- **Backend**: PHP 8.0+
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework**: Bootstrap 5.3.2
- **Icons**: Font Awesome 6.4.0
- **Animations**: AOS (Animate On Scroll)
- **AI Features**: Python with TensorFlow/OpenCV

## 📋 Prerequisites

Before running this project, make sure you have the following installed:

- **Web Server**: Apache/Nginx
- **PHP**: 8.0 or higher
- **MySQL**: 8.0 or higher
- **Python**: 3.8+ (for AI features)
- **Composer**: (optional, for dependency management)

## 🚀 Installation

### Step 1: Clone the Repository
```bash
git clone https://github.com/yourusername/flexfusion-gym-management.git
cd flexfusion-gym-management
```

### Step 2: Database Setup
1. Create a MySQL database:
```sql
CREATE DATABASE flexfusion_db;
```

2. Import the database schema:
```bash
mysql -u your_username -p flexfusion_db < sql/flexfusion_schema.sql
```

3. Import sample data (optional):
```bash
mysql -u your_username -p flexfusion_db < sql/insert_plans.sql
mysql -u your_username -p flexfusion_db < sql/insert_subscriptions.sql
```

### Step 3: Configuration
1. Navigate to the `includes/` directory and update database configuration:
```php
// includes/config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'flexfusion_db');
```

### Step 4: Web Server Configuration
1. Point your web server to the project directory
2. Ensure PHP has write permissions for uploads and logs
3. Configure URL rewriting if needed

### Step 5: AI Features Setup (Optional)
```bash
cd ai_coach
pip install -r requirements.txt
```

## 📁 Project Structure

```
flexfusion-gym-management/
├── admin/                 # Admin panel files
│   ├── dashboard.php
│   ├── manage_members.php
│   ├── manage_trainers.php
│   └── ...
├── member/               # Member panel files
│   ├── dashboard.php
│   ├── my_workout.php
│   ├── my_progress.php
│   └── ...
├── trainer/              # Trainer panel files
│   ├── dashboard.php
│   ├── my_members.php
│   ├── track_progress.php
│   └── ...
├── auth/                 # Authentication files
├── includes/             # Common includes and functions
├── assets/               # Static assets (CSS, JS, images)
├── ajax/                 # AJAX handlers
├── ai_coach/             # AI features
│   ├── chatbot.py
│   └── simple_chatbot.py
├── database/             # Database-related files
├── sql/                  # SQL schema and data
│   ├── flexfusion_schema.sql
│   ├── insert_plans.sql
│   └── insert_subscriptions.sql
├── js/                   # JavaScript files
├── index.php             # Main landing page
└── README.md
```

## 🔐 Default Login Credentials

### Admin
- **Email**: admin@flexfusion.com
- **Password**: admin123

### Trainer
- **Email**: trainer@flexfusion.com
- **Password**: trainer123

### Member
- **Email**: member@flexfusion.com
- **Password**: member123

> ⚠️ **Important**: Change default passwords after first login for security.

## 🎯 Usage Guide

### For Administrators
1. **Dashboard**: View system overview and key metrics
2. **Member Management**: Add new members, edit profiles, track subscriptions
3. **Trainer Management**: Assign trainers to members, monitor performance
4. **Reports**: Generate attendance, financial, and progress reports

### For Trainers
1. **My Members**: View assigned members and their progress
2. **Workout Management**: Create and assign personalized workout plans
3. **Progress Tracking**: Monitor member fitness goals and achievements
4. **Communication**: Chat with members and provide guidance

### For Members
1. **Dashboard**: View assigned plans and recent activities
2. **My Workout**: Access workout plans and track exercises
3. **My Diet**: View nutrition plans and track food intake
4. **Progress**: Record measurements and track fitness goals
5. **AI Coach**: Get personalized fitness advice and recommendations

## 🤖 AI Features

The system includes advanced AI capabilities:

- **Smart Workout Analysis**: Real-time form analysis using computer vision
- **Personalized Recommendations**: ML-based workout and diet suggestions
- **Progress Prediction**: AI-powered goal achievement forecasting
- **Virtual Training Assistant**: Natural language processing for fitness guidance

## 🔧 Configuration

### Database Configuration
Update `includes/config.php` with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'flexfusion_db');
```

### Email Configuration
Configure email settings for notifications:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_email@gmail.com');
define('SMTP_PASS', 'your_app_password');
```

## 🧪 Testing

### Manual Testing
1. Test all user roles (Admin, Trainer, Member)
2. Verify CRUD operations for all entities
3. Test communication features
4. Validate progress tracking functionality

### Automated Testing
```bash
# Run PHP unit tests (if configured)
php vendor/bin/phpunit tests/
```

## 🚀 Deployment

### Production Deployment
1. **Environment Setup**:
   - Configure production database
   - Set up SSL certificate
   - Configure web server (Apache/Nginx)

2. **Security Measures**:
   - Change default passwords
   - Enable HTTPS
   - Configure firewall rules
   - Set up regular backups

3. **Performance Optimization**:
   - Enable PHP OPcache
   - Configure MySQL optimization
   - Set up CDN for static assets

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

If you encounter any issues or have questions:

1. Check the [Issues](https://github.com/yourusername/flexfusion-gym-management/issues) page
2. Create a new issue with detailed description
3. Contact the development team

## 🔮 Future Enhancements

- [ ] Mobile Application (iOS/Android)
- [ ] Payment Gateway Integration
- [ ] Social Media Integration
- [ ] Advanced Analytics Dashboard
- [ ] Video Call Sessions
- [ ] Workout Video Library
- [ ] Nutrition Database Integration
- [ ] Automated Progress Reports
- [ ] AR/VR Integration
- [ ] Blockchain-based Achievement Tracking

## 📊 System Requirements

### Minimum Requirements
- **PHP**: 8.0+
- **MySQL**: 8.0+
- **RAM**: 2GB
- **Storage**: 1GB
- **Browser**: Chrome 90+, Firefox 88+, Safari 14+

### Recommended Requirements
- **PHP**: 8.1+
- **MySQL**: 8.0+
- **RAM**: 4GB+
- **Storage**: 5GB+
- **Browser**: Latest versions

## 🏆 Acknowledgments

- Bootstrap for the responsive UI framework
- Font Awesome for the icon library
- AOS for scroll animations
- OpenCV and MediaPipe for AI features
- All contributors and testers

---

<div align="center">
  <p>Made with ❤️ by the FlexFusion Team</p>
  <p>Transform your fitness journey with FlexFusion!</p>
</div>
