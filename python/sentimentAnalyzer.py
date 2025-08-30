# english_sentiment.py

import sys
import nltk
from nltk.sentiment.vader import SentimentIntensityAnalyzer

# Download VADER lexicon only once
nltk.download('vader_lexicon', quiet=True)

# Initialize VADER
sid = SentimentIntensityAnalyzer()

def analyze_product_comment(comment):
    scores = sid.polarity_scores(comment)
    compound_score = scores['compound']
    
    if compound_score >= 0.05:
        return "Positive"
    elif compound_score <= -0.05:
        return "Negative"
    else:
        return "Neutral"

if __name__ == "__main__":
    # Get input from Laravel
    comment = sys.argv[1]
    result = analyze_product_comment(comment)
    print(result)