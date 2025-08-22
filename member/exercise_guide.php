<?php
session_start();
require_once '../includes/db.php';

// Only members allowed
if ($_SESSION['role'] !== 'member') header("Location: ../auth/login.php");
$member_id = $_SESSION['user_id'];

// Fetch member's workout plan to get relevant exercises
$stmt = $pdo->prepare("
    SELECT wp.title, wp.description 
    FROM member_plans mp
    JOIN workout_plans wp ON mp.workout_plan_id = wp.plan_id
    WHERE mp.member_id = ?
    ORDER BY mp.assigned_at DESC
    LIMIT 1
");
$stmt->execute([$member_id]);
$workout = $stmt->fetch();

// Exercise categories
$categories = [
    'strength' => [
        'title' => 'Strength Training',
        'icon' => 'dumbbell',
        'exercises' => [
            [
                'name' => 'Bench Press',
                'difficulty' => 'Intermediate',
                'muscle_groups' => ['Chest', 'Triceps', 'Shoulders'],
                'video_url' => 'https://www.youtube.com/embed/rT7DgCr-3pg',
                'steps' => [
                    'Lie on the bench with feet flat on the floor',
                    'Grip the bar slightly wider than shoulder-width',
                    'Lower the bar to mid-chest',
                    'Push the bar back up to starting position'
                ],
                'tips' => [
                    'Keep your back flat against the bench',
                    'Maintain a slight arch in your lower back',
                    'Keep your elbows at a 45-degree angle'
                ]
            ],
            [
                'name' => 'Squats',
                'difficulty' => 'Beginner',
                'muscle_groups' => ['Legs', 'Glutes', 'Core'],
                'video_url' => 'https://www.youtube.com/embed/YaXPRqUwItQ',
                'steps' => [
                    'Stand with feet shoulder-width apart',
                    'Keep chest up and core tight',
                    'Lower body until thighs are parallel to ground',
                    'Push through heels to return to starting position'
                ],
                'tips' => [
                    'Keep knees aligned with toes',
                    'Maintain neutral spine',
                    'Breathe steadily throughout movement'
                ]
            ]
        ]
    ],
    'cardio' => [
        'title' => 'Cardio Exercises',
        'icon' => 'heartbeat',
        'exercises' => [
            [
                'name' => 'HIIT Workout',
                'difficulty' => 'Advanced',
                'muscle_groups' => ['Full Body', 'Cardiovascular'],
                'video_url' => 'https://www.youtube.com/embed/ML6W7RrxoCI',
                'steps' => [
                    'Warm up for 5 minutes',
                    '30 seconds high-intensity exercise',
                    '30 seconds rest',
                    'Repeat for 20 minutes'
                ],
                'tips' => [
                    'Stay hydrated',
                    'Maintain proper form',
                    'Adjust intensity based on fitness level'
                ]
            ]
        ]
    ],
    'flexibility' => [
        'title' => 'Flexibility & Mobility',
        'icon' => 'child',
        'exercises' => [
            [
                'name' => 'Dynamic Stretching',
                'difficulty' => 'Beginner',
                'muscle_groups' => ['Full Body'],
                'video_url' => 'https://www.youtube.com/embed/2tM1LFFxeKg',
                'steps' => [
                    'Start with arm circles',
                    'Move to hip circles',
                    'Perform walking knee hugs',
                    'Finish with walking lunges'
                ],
                'tips' => [
                    'Move slowly and controlled',
                    'Don\'t force any movements',
                    'Breathe deeply throughout'
                ]
            ]
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercise Guide | FlexFusion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
        }
        
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--light-color);
        }
        
        .sidebar {
            height: 100vh;
            background: linear-gradient(180deg, var(--dark-color), #34495e);
            padding-top: 20px;
            position: fixed;
            width: 250px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #ffffffcc;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .sidebar a:hover, .sidebar a.active {
            background-color: var(--primary-color);
            color: white;
            border-left-color: white;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
        
        .category-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .exercise-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .steps-list {
            list-style: none;
            padding-left: 0;
        }
        
        .steps-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .steps-list li:last-child {
            border-bottom: none;
        }
        
        .tips-section {
            background: rgba(52, 152, 219, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .difficulty-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .difficulty-beginner {
            background: var(--success-color);
            color: white;
        }
        
        .difficulty-intermediate {
            background: var(--warning-color);
            color: white;
        }
        
        .difficulty-advanced {
            background: var(--danger-color);
            color: white;
        }
        
        .muscle-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 0.5rem 0;
        }
        
        .muscle-tag {
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .ai-features {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .ai-feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .ai-feature-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center text-white mb-4">
        <i class="fas fa-dumbbell me-2"></i>Member Panel
    </h4>
    <a href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
    <a href="my_workout.php"><i class="fas fa-dumbbell me-2"></i>My Workout</a>
    <a href="my_diet.php"><i class="fas fa-utensils me-2"></i>My Diet</a>
    <a href="my_progress.php"><i class="fas fa-chart-line me-2"></i>My Progress</a>
    <a href="attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
    <a href="chat.php"><i class="fas fa-comments me-2"></i>Chat</a>
    <a href="feedback.php"><i class="fas fa-comment-dots me-2"></i>Feedback</a>
    <a href="exercise_guide.php" class="active"><i class="fas fa-dumbbell me-2"></i>Exercise Guide</a>
    <a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <h2><i class="fas fa-dumbbell me-2"></i>AI-Powered Exercise Guide</h2>
        <p class="text-muted">Learn proper form and technique with our smart exercise guide</p>

        <!-- AI Features Section -->
        <div class="ai-features">
            <h4 class="mb-4">Smart Features</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="ai-feature-item">
                        <div class="ai-feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Form Analysis</h6>
                            <small>Get real-time feedback on your exercise form</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="ai-feature-item">
                        <div class="ai-feature-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Personalized Recommendations</h6>
                            <small>AI-powered exercise suggestions based on your goals</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="ai-feature-item">
                        <div class="ai-feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Progress Tracking</h6>
                            <small>Track your improvement with smart analytics</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exercise Categories -->
        <?php foreach ($categories as $category): ?>
        <div class="category-card">
            <h3><i class="fas fa-<?= $category['icon'] ?> me-2"></i><?= $category['title'] ?></h3>
            
            <?php foreach ($category['exercises'] as $exercise): ?>
            <div class="exercise-card">
                <div class="d-flex justify-content-between align-items-start">
                    <h4><?= $exercise['name'] ?></h4>
                    <span class="difficulty-badge difficulty-<?= strtolower($exercise['difficulty']) ?>">
                        <?= $exercise['difficulty'] ?>
                    </span>
                </div>
                
                <div class="muscle-tags">
                    <?php foreach ($exercise['muscle_groups'] as $muscle): ?>
                    <span class="muscle-tag"><?= $muscle ?></span>
                    <?php endforeach; ?>
                </div>
                
                <div class="video-container">
                    <iframe src="<?= $exercise['video_url'] ?>" allowfullscreen></iframe>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>Steps:</h5>
                        <ul class="steps-list">
                            <?php foreach ($exercise['steps'] as $step): ?>
                            <li><i class="fas fa-check-circle text-success me-2"></i><?= $step ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <div class="tips-section">
                            <h5>Pro Tips:</h5>
                            <ul class="steps-list">
                                <?php foreach ($exercise['tips'] as $tip): ?>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i><?= $tip ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 