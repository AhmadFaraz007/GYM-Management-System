import torch
from transformers import AutoModelForCausalLM, AutoTokenizer
import json
import numpy as np
from datetime import datetime
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

class FitnessCoach:
    def __init__(self):
        # Load the pre-trained model and tokenizer
        self.model_name = "microsoft/DialoGPT-medium"
        self.tokenizer = AutoTokenizer.from_pretrained(self.model_name)
        self.model = AutoModelForCausalLM.from_pretrained(self.model_name)
        
        # Load fitness knowledge base
        self.knowledge_base = self.load_knowledge_base()
        
        # Initialize conversation history
        self.conversation_history = []
        
        # Load user preferences and goals
        self.user_preferences = {}
        
    def load_knowledge_base(self):
        return {
            "workout": {
                "strength": [
                    "Focus on compound exercises like squats, deadlifts, and bench press",
                    "Aim for 3-4 sets of 8-12 reps for muscle growth",
                    "Rest 60-90 seconds between sets",
                    "Progressive overload is key to strength gains"
                ],
                "cardio": [
                    "Start with 20-30 minutes of moderate intensity cardio",
                    "HIIT workouts are great for fat loss",
                    "Aim for 150 minutes of cardio per week",
                    "Mix different types of cardio for best results"
                ],
                "flexibility": [
                    "Stretch after your workout when muscles are warm",
                    "Hold each stretch for 30 seconds",
                    "Focus on major muscle groups",
                    "Don't bounce while stretching"
                ]
            },
            "nutrition": {
                "weight_loss": [
                    "Create a caloric deficit of 500 calories per day",
                    "Focus on protein-rich foods",
                    "Stay hydrated with water",
                    "Limit processed foods and sugars"
                ],
                "muscle_gain": [
                    "Eat in a caloric surplus",
                    "Consume 1.6-2.2g of protein per kg of bodyweight",
                    "Eat every 3-4 hours",
                    "Include complex carbs in your diet"
                ],
                "general": [
                    "Eat whole, unprocessed foods",
                    "Stay hydrated",
                    "Balance your macronutrients",
                    "Don't skip meals"
                ]
            },
            "motivation": [
                "Set specific, measurable goals",
                "Track your progress",
                "Find a workout buddy",
                "Celebrate small victories",
                "Stay consistent with your routine"
            ],
            "recovery": [
                "Get 7-9 hours of sleep",
                "Stay hydrated",
                "Take rest days",
                "Practice stress management",
                "Consider foam rolling and stretching"
            ]
        }
    
    def get_relevant_advice(self, user_input):
        # Convert input to lowercase for better matching
        input_lower = user_input.lower()
        
        # Initialize response components
        response_components = []
        
        # Check for workout-related queries
        if any(word in input_lower for word in ['workout', 'exercise', 'training', 'gym']):
            if 'strength' in input_lower:
                response_components.extend(self.knowledge_base['workout']['strength'])
            elif 'cardio' in input_lower:
                response_components.extend(self.knowledge_base['workout']['cardio'])
            elif 'flexibility' in input_lower:
                response_components.extend(self.knowledge_base['workout']['flexibility'])
        
        # Check for nutrition-related queries
        if any(word in input_lower for word in ['diet', 'nutrition', 'food', 'eat']):
            if 'weight' in input_lower and ('loss' in input_lower or 'lose' in input_lower):
                response_components.extend(self.knowledge_base['nutrition']['weight_loss'])
            elif 'muscle' in input_lower and ('gain' in input_lower or 'build' in input_lower):
                response_components.extend(self.knowledge_base['nutrition']['muscle_gain'])
            else:
                response_components.extend(self.knowledge_base['nutrition']['general'])
        
        # Check for motivation-related queries
        if any(word in input_lower for word in ['motivation', 'motivated', 'inspire', 'inspiration']):
            response_components.extend(self.knowledge_base['motivation'])
        
        # Check for recovery-related queries
        if any(word in input_lower for word in ['recovery', 'rest', 'sleep', 'tired']):
            response_components.extend(self.knowledge_base['recovery'])
        
        # If no specific category is matched, provide general advice
        if not response_components:
            response_components.extend(self.knowledge_base['motivation'])
            response_components.extend(self.knowledge_base['nutrition']['general'])
        
        return response_components
    
    def generate_response(self, user_input):
        # Get relevant advice based on user input
        advice_components = self.get_relevant_advice(user_input)
        
        # Add user input to conversation history
        self.conversation_history.append({"role": "user", "content": user_input})
        
        # Generate response using the model
        input_ids = self.tokenizer.encode(user_input + self.tokenizer.eos_token, return_tensors='pt')
        response_ids = self.model.generate(
            input_ids,
            max_length=1000,
            pad_token_id=self.tokenizer.eos_token_id,
            no_repeat_ngram_size=3,
            do_sample=True,
            top_k=100,
            top_p=0.7,
            temperature=0.8
        )
        
        # Decode the response
        response = self.tokenizer.decode(response_ids[:, input_ids.shape[-1]:][0], skip_special_tokens=True)
        
        # Combine model response with relevant advice
        if advice_components:
            advice = np.random.choice(advice_components)
            response = f"{response}\n\nHere's some specific advice: {advice}"
        
        # Add response to conversation history
        self.conversation_history.append({"role": "assistant", "content": response})
        
        return response
    
    def update_user_preferences(self, preferences):
        self.user_preferences.update(preferences)
    
    def get_personalized_recommendation(self):
        if not self.user_preferences:
            return "I don't have enough information about your preferences yet. Could you tell me more about your fitness goals?"
        
        recommendations = []
        
        if 'goal' in self.user_preferences:
            goal = self.user_preferences['goal']
            if goal == 'weight_loss':
                recommendations.extend(self.knowledge_base['nutrition']['weight_loss'])
            elif goal == 'muscle_gain':
                recommendations.extend(self.knowledge_base['nutrition']['muscle_gain'])
        
        if 'workout_type' in self.user_preferences:
            workout_type = self.user_preferences['workout_type']
            if workout_type in self.knowledge_base['workout']:
                recommendations.extend(self.knowledge_base['workout'][workout_type])
        
        if recommendations:
            return "Based on your preferences, here's what I recommend:\n" + "\n".join(recommendations)
        else:
            return "I need more information about your preferences to provide personalized recommendations."

# Create a singleton instance
fitness_coach = FitnessCoach() 