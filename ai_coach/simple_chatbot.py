#!/usr/bin/env python3
import sys
import json
import random
from datetime import datetime

class SimpleFitnessCoach:
    def __init__(self):
        self.knowledge_base = {
            "workout": {
                "strength": [
                    "For strength training, focus on compound exercises like squats, deadlifts, and bench press. Aim for 3-4 sets of 8-12 reps with 60-90 seconds rest between sets.",
                    "Progressive overload is key to building strength. Gradually increase weight or reps over time.",
                    "Make sure to warm up properly before strength training to prevent injuries."
                ],
                "cardio": [
                    "For cardio, start with 20-30 minutes of moderate intensity exercise. You can do running, cycling, or swimming.",
                    "HIIT (High-Intensity Interval Training) is great for burning fat. Try 30 seconds of high intensity followed by 30 seconds of rest.",
                    "Aim for at least 150 minutes of moderate cardio per week for optimal health."
                ],
                "flexibility": [
                    "Stretch after your workout when your muscles are warm. Hold each stretch for 30 seconds.",
                    "Focus on major muscle groups and don't bounce while stretching.",
                    "Yoga and pilates are excellent for improving flexibility and core strength."
                ]
            },
            "nutrition": {
                "weight_loss": [
                    "To lose weight, create a caloric deficit of 500 calories per day. This will help you lose about 1 pound per week.",
                    "Focus on protein-rich foods like lean meats, fish, eggs, and legumes.",
                    "Stay hydrated by drinking at least 8 glasses of water daily.",
                    "Limit processed foods and added sugars in your diet."
                ],
                "muscle_gain": [
                    "To build muscle, eat in a caloric surplus and consume 1.6-2.2g of protein per kg of bodyweight.",
                    "Eat every 3-4 hours to maintain steady protein synthesis.",
                    "Include complex carbs like brown rice, quinoa, and sweet potatoes in your diet.",
                    "Don't forget healthy fats from sources like nuts, avocados, and olive oil."
                ],
                "general": [
                    "Eat whole, unprocessed foods as much as possible.",
                    "Stay hydrated throughout the day.",
                    "Balance your macronutrients: protein, carbs, and fats.",
                    "Don't skip meals, especially breakfast."
                ]
            },
            "motivation": [
                "Set specific, measurable goals and track your progress regularly.",
                "Find a workout buddy to keep you accountable and motivated.",
                "Celebrate small victories along your fitness journey.",
                "Remember that consistency is more important than perfection.",
                "Focus on how exercise makes you feel, not just how it makes you look."
            ],
            "recovery": [
                "Get 7-9 hours of quality sleep each night for optimal recovery.",
                "Stay hydrated and consider electrolyte replacement after intense workouts.",
                "Take rest days to allow your body to recover and prevent overtraining.",
                "Practice stress management techniques like meditation or deep breathing.",
                "Consider foam rolling and stretching to improve muscle recovery."
            ],
            "general": [
                "Start slowly and gradually increase intensity to prevent injuries.",
                "Listen to your body and don't push through pain.",
                "Consistency is key - even short workouts are better than none.",
                "Find activities you enjoy to make fitness sustainable.",
                "Remember that everyone's fitness journey is unique."
            ]
        }
    
    def get_response(self, user_input):
        user_input_lower = user_input.lower()
        
        # Check for workout-related queries
        if any(word in user_input_lower for word in ['workout', 'exercise', 'training', 'gym', 'lift']):
            if 'strength' in user_input_lower or 'muscle' in user_input_lower:
                return random.choice(self.knowledge_base['workout']['strength'])
            elif 'cardio' in user_input_lower or 'run' in user_input_lower or 'bike' in user_input_lower:
                return random.choice(self.knowledge_base['workout']['cardio'])
            elif 'flexibility' in user_input_lower or 'stretch' in user_input_lower:
                return random.choice(self.knowledge_base['workout']['flexibility'])
            else:
                return random.choice(self.knowledge_base['workout']['strength'])
        
        # Check for nutrition-related queries
        elif any(word in user_input_lower for word in ['diet', 'nutrition', 'food', 'eat', 'meal']):
            if 'weight' in user_input_lower and ('loss' in user_input_lower or 'lose' in user_input_lower):
                return random.choice(self.knowledge_base['nutrition']['weight_loss'])
            elif 'muscle' in user_input_lower and ('gain' in user_input_lower or 'build' in user_input_lower):
                return random.choice(self.knowledge_base['nutrition']['muscle_gain'])
            else:
                return random.choice(self.knowledge_base['nutrition']['general'])
        
        # Check for motivation-related queries
        elif any(word in user_input_lower for word in ['motivation', 'motivated', 'inspire', 'inspiration', 'tired', 'lazy']):
            return random.choice(self.knowledge_base['motivation'])
        
        # Check for recovery-related queries
        elif any(word in user_input_lower for word in ['recovery', 'rest', 'sleep', 'tired', 'sore']):
            return random.choice(self.knowledge_base['recovery'])
        
        # Default response
        else:
            return random.choice(self.knowledge_base['general'])

def main():
    if len(sys.argv) < 2:
        print("Please provide a message as an argument.")
        sys.exit(1)
    
    user_message = sys.argv[1]
    coach = SimpleFitnessCoach()
    response = coach.get_response(user_message)
    print(response)

if __name__ == "__main__":
    main() 