-- Get admin user ID
SET @admin_id = (SELECT user_id FROM users WHERE role = 'admin' LIMIT 1);

-- Insert Diet Plans
INSERT INTO diet_plans (title, description, created_by) VALUES
('Weight Loss Diet Plan', 'A balanced diet plan focused on healthy weight loss:
• Breakfast: Oatmeal with fruits and nuts (300 calories)
• Mid-morning: Greek yogurt with berries (150 calories)
• Lunch: Grilled chicken salad with olive oil dressing (400 calories)
• Afternoon snack: Apple with almond butter (200 calories)
• Dinner: Baked fish with steamed vegetables (350 calories)
Total daily calories: 1400
Macros: 40% Protein, 30% Carbs, 30% Fats
Stay hydrated with 2-3 liters of water daily.', @admin_id),

('Muscle Building Diet', 'High-protein diet plan for muscle growth:
• Breakfast: Protein pancakes with banana and honey (500 calories)
• Mid-morning: Protein shake with peanut butter (300 calories)
• Lunch: Brown rice with grilled chicken and vegetables (600 calories)
• Pre-workout: Banana with almonds (250 calories)
• Post-workout: Protein shake with oats (400 calories)
• Dinner: Steak with sweet potato and broccoli (700 calories)
Total daily calories: 2750
Macros: 40% Protein, 40% Carbs, 20% Fats
Drink 3-4 liters of water daily.', @admin_id),

('Vegetarian Fitness Diet', 'Plant-based diet for active individuals:
• Breakfast: Tofu scramble with whole grain toast (400 calories)
• Mid-morning: Mixed nuts and dried fruits (200 calories)
• Lunch: Quinoa bowl with chickpeas and vegetables (500 calories)
• Afternoon: Hummus with whole grain crackers (250 calories)
• Dinner: Lentil curry with brown rice (550 calories)
Total daily calories: 1900
Macros: 25% Protein, 55% Carbs, 20% Fats
Include 2-3 liters of water and herbal teas.', @admin_id),

('Keto Diet Plan', 'Low-carb, high-fat diet plan:
• Breakfast: Avocado and eggs with bacon (450 calories)
• Mid-morning: Cheese and nuts (200 calories)
• Lunch: Grilled salmon with avocado salad (500 calories)
• Afternoon: Keto fat bombs (150 calories)
• Dinner: Chicken thighs with cauliflower rice (550 calories)
Total daily calories: 1850
Macros: 30% Protein, 5% Carbs, 65% Fats
Maintain electrolyte balance with 2-3 liters of water.', @admin_id),

('Balanced Maintenance Diet', 'Well-rounded diet for maintaining fitness:
• Breakfast: Whole grain toast with eggs and avocado (400 calories)
• Mid-morning: Fruit smoothie with protein (250 calories)
• Lunch: Turkey wrap with vegetables (450 calories)
• Afternoon: Greek yogurt with granola (200 calories)
• Dinner: Baked chicken with quinoa and vegetables (500 calories)
Total daily calories: 1800
Macros: 35% Protein, 45% Carbs, 20% Fats
Stay hydrated with 2-3 liters of water daily.', @admin_id);

-- Insert Workout Plans
INSERT INTO workout_plans (title, description, created_by) VALUES
('Full Body Strength Training', 'Complete full-body workout routine:
• Warm-up: 10 minutes cardio (jogging/cycling)
• Squats: 4 sets x 12 reps
• Bench Press: 4 sets x 10 reps
• Deadlifts: 4 sets x 8 reps
• Pull-ups: 3 sets x 8-10 reps
• Shoulder Press: 3 sets x 12 reps
• Plank: 3 sets x 60 seconds
• Cool-down: 10 minutes stretching
Rest 60-90 seconds between sets
Train 3-4 times per week
Focus on proper form and controlled movements', @admin_id),

('HIIT Cardio Blast', 'High-intensity interval training program:
• Warm-up: 5 minutes light cardio
• Circuit (repeat 4 times):
  - Jumping jacks: 45 seconds
  - Mountain climbers: 45 seconds
  - Burpees: 45 seconds
  - High knees: 45 seconds
  - Rest: 60 seconds
• Core circuit:
  - Crunches: 3 sets x 20 reps
  - Russian twists: 3 sets x 20 reps
  - Leg raises: 3 sets x 15 reps
• Cool-down: 10 minutes stretching
Train 3-4 times per week
Modify intensity based on fitness level', @admin_id),

('Upper Body Focus', 'Targeted upper body workout:
• Warm-up: 10 minutes cardio
• Bench Press: 4 sets x 10 reps
• Pull-ups: 4 sets x 8-10 reps
• Shoulder Press: 4 sets x 12 reps
• Bicep Curls: 3 sets x 12 reps
• Tricep Dips: 3 sets x 15 reps
• Lateral Raises: 3 sets x 15 reps
• Face Pulls: 3 sets x 15 reps
• Cool-down: 10 minutes stretching
Rest 60-90 seconds between sets
Train 2-3 times per week
Focus on mind-muscle connection', @admin_id),

('Lower Body Power', 'Intense lower body workout:
• Warm-up: 10 minutes cardio
• Squats: 5 sets x 8-10 reps
• Romanian Deadlifts: 4 sets x 10 reps
• Leg Press: 4 sets x 12 reps
• Walking Lunges: 3 sets x 20 steps
• Calf Raises: 4 sets x 15 reps
• Leg Extensions: 3 sets x 15 reps
• Leg Curls: 3 sets x 15 reps
• Cool-down: 10 minutes stretching
Rest 90-120 seconds between sets
Train 2 times per week
Focus on progressive overload', @admin_id),

('Core & Stability', 'Comprehensive core workout:
• Warm-up: 5 minutes cardio
• Plank Variations:
  - Standard plank: 3 sets x 60 seconds
  - Side plank: 3 sets x 45 seconds each side
  - Reverse plank: 3 sets x 45 seconds
• Core Circuit (repeat 3 times):
  - Crunches: 20 reps
  - Russian twists: 20 reps
  - Bicycle crunches: 20 reps
  - Leg raises: 15 reps
  - Rest: 60 seconds
• Stability Exercises:
  - Bird dogs: 3 sets x 12 reps each side
  - Dead bugs: 3 sets x 12 reps each side
• Cool-down: 10 minutes stretching
Train 3-4 times per week
Focus on controlled movements and breathing', @admin_id); 