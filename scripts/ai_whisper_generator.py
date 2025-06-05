import random
import re

# Keywords that might be found in bios, and associated descriptive terms
BIO_KEYWORDS_MAP = {
    "conde": ["nobility", "leadership", "governance"],
    "batalla": ["valor", "conflict", "strategy"],
    "rey": ["sovereignty", "power", "command"],
    "reina": ["majesty", "grace", "influence"],
    "fe": ["devotion", "piety", "spirituality"],
    "leyenda": ["myth", "folklore", "enigma"],
    "misterio": ["secrets", "the unknown", "intrigue"],
    "fundador": ["origins", "beginnings", "legacy"],
    "santo": ["holiness", "virtue", "miracles"],
    "mártir": ["sacrifice", "conviction", "faith"],
    "gobernante": ["rule", "authority", "dominion"],
    "constructor": ["creation", "foundations", "ingenuity"],
    "defensor": ["protection", "guardianship", "fortitude"],
    "explorador": ["discovery", "journeys", "frontiers"],
    "sabio": ["wisdom", "knowledge", "insight"]
}

# Poetic phrases - can be expanded
POETIC_PHRASES = [
    "a shadow and a flame",
    "a whisper in the ruins",
    "an echo in the stones",
    "a legend woven in time",
    "a beacon in the mists of history",
    "a forgotten name, now remembered",
    "a story etched in the heart of the land"
]

ERA_KEYWORDS = [
    "a forgotten age", "an era of upheaval", "a time of nascent kingdoms",
    "an age of empires", "a period of profound faith", "an epoch of legends"
]


def extract_bio_keyword_and_theme(bio_snippet):
    if not bio_snippet:
        return "an unknown quality", "an unknown theme"

    text = bio_snippet.lower()
    found_keywords = {} # keyword -> count

    for keyword_stem, themes in BIO_KEYWORDS_MAP.items():
        if re.search(r'\b' + re.escape(keyword_stem) + r'\w*\b', text):
            for theme in themes:
                found_keywords[theme] = found_keywords.get(theme, 0) + 1

    if found_keywords:
        # Prefer keywords that appear more often or are more "specific"
        # This is a simple heuristic, could be improved
        most_relevant_theme = max(found_keywords, key=found_keywords.get)
        # Try to find the actual word in the bio that triggered this theme for a more natural keyword
        for keyword_stem, themes in BIO_KEYWORDS_MAP.items():
            if most_relevant_theme in themes:
                match = re.search(r'\b(' + re.escape(keyword_stem) + r'\w*)\b', text)
                if match:
                    return match.group(1), most_relevant_theme
        return most_relevant_theme, most_relevant_theme # Fallback to theme itself

    # Fallback: look for any capitalized word (potential noun/title) not at the start of a sentence
    capitalized_words = re.findall(r'(?<!\.\s)\b[A-Z][a-z]+\b', bio_snippet)
    if capitalized_words:
        keyword = random.choice(capitalized_words).lower()
        return keyword, keyword # Use keyword as its own theme for simplicity

    return "a notable deed", "their impact"


def generate_whisper(character_data):
    if not character_data:
        return "A forgotten soul, whose story is yet to be fully unveiled."

    name = character_data.get('name', 'A forgotten figure')
    bio_snippet = character_data.get('bio_snippet', '')
    key_facts = character_data.get('key_facts', [])

    fact = ""
    if key_facts:
        fact = random.choice(key_facts)
        # Clean up fact a bit, remove trailing periods for template insertion
        fact = fact.strip().rstrip('.').lower()
    else:
        fact = "their unrecorded deeds"

    bio_keyword, bio_theme = extract_bio_keyword_and_theme(bio_snippet)

    poetic_phrase = random.choice(POETIC_PHRASES)
    era_keyword = random.choice(ERA_KEYWORDS)
    description_keyword = bio_theme # Use the theme from bio_keyword extraction

    templates = [
        f"Hark, for {name} once walked these lands, leaving whispers of {fact} in the ancient stones.",
        f"They say the very winds in Cerasio still murmur of {name} and their legendary {bio_keyword}.",
        f"Remember {name}? A soul intertwined with the fate of these ruins, known for {fact}.",
        f"'{poetic_phrase.capitalize()}' - thus is {name} remembered in the chronicles of old.",
        f"The legacy of {name} is etched in time, a tale of {bio_keyword} and glory.",
        f"From {era_keyword}, {name} emerges, a figure of {description_keyword}.",
        f"Let the stones of old speak of {name}, and of the time they {fact}.",
        f"Lost to time, yet {name} lingers, a whisper of {bio_keyword} from a bygone era.",
        f"The chronicles speak of {name}, whose life was marked by {fact} and tales of {description_keyword}.",
        f"Beneath the dust of ages lies the story of {name}, a narrative of {bio_keyword}.",
        f"Consider {name}, whose actions concerning {fact} shaped the contours of this land.",
        f"In the shadow of ancient fortresses, the memory of {name} and their {bio_keyword} endures.",
        f"Ask the silent hills of {name}; they might recall {fact} from an age of {description_keyword}.",
        f"'{name}, {poetic_phrase},' or so the bards once sang, their verses echoing {fact}.",
        f"Unearth the story of {name}: a chapter from {era_keyword}, a testament to {bio_keyword}."
    ]

    # Handle cases where name might be very long or contain titles already
    if len(name) > 30 and (name.startswith("Conde") or name.startswith("Doña") or name.startswith("San")):
        name_parts = name.split()
        if len(name_parts) > 2: # e.g. "Conde Fernán González" -> "Fernán González"
            name_short = " ".join(name_parts[1:])
            templates.extend([
                f"Whispers call to {name_short}, a name synonymous with {fact}.",
                f"The spirit of {name_short} is said to linger, a guardian of {bio_keyword}."
            ])
        else: # "San Formerio" -> "Formerio"
             name_short = name_parts[-1]
             templates.extend([
                f"They speak of {name_short}, whose life was a testament to {bio_keyword}."
             ])


    # Special template for characters with no specific fact but a strong bio keyword
    if fact == "their unrecorded deeds" and bio_keyword != "a notable deed":
        templates.append(f"Though details are lost, the essence of {name} – their {bio_keyword} – remains palpable.")

    return random.choice(templates)


if __name__ == '__main__':
    sample_data_1 = {
        'name': 'Rodrigo el Conde',
        'bio_snippet': 'Rodrigo, a prominent count in early Castile, played a significant role in the defense and expansion of the county. His leadership in battle was legendary.',
        'key_facts': ['Repopulated Amaya in 860', 'Fought at the Battle of Morcuera'],
        'file_path': 'path/to/rodrigo.html'
    }

    sample_data_2 = {
        'name': 'Doña Sancha de Pamplona',
        'bio_snippet': 'Queen consort of León, known for her piety and influence in religious matters. Her faith was a cornerstone of her reign.',
        'key_facts': ['Married Fernando I of León', 'Patron of several monasteries', 'Influenced religious policy'],
        'file_path': 'path/to/sancha.html'
    }

    sample_data_3 = {
        'name': 'Corocotta',
        'bio_snippet': 'A Cantabrian warrior chief who famously defied Roman rule. His story is a blend of history and legend, a true mystery.',
        'key_facts': [], # No specific facts, will use generic
        'file_path': 'path/to/corocotta.html'
    }

    sample_data_4 = {
        'name': 'San Formerio, Mártir de Cerasio',
        'bio_snippet': 'San Formerio is a revered martyr in the region of Cerasio (Cerezo de Río Tirón). His steadfast faith in the face of persecution led to his martyrdom, and he is venerated for his sacrifice and miracles.',
        'key_facts': ['Preached Christianity in Auca Patricia and Cerasio', 'Martyred for his faith', 'Relics venerated in Cerezo and elsewhere'],
        'file_path': 'path/to/formerio.html'
    }

    print("--- Generating Whispers ---")
    for i in range(3): # Generate a few for each to see variety
        print(f"\nWhisper for {sample_data_1['name']} ({i+1}):")
        print(generate_whisper(sample_data_1))

    for i in range(3):
        print(f"\nWhisper for {sample_data_2['name']} ({i+1}):")
        print(generate_whisper(sample_data_2))

    for i in range(3):
        print(f"\nWhisper for {sample_data_3['name']} ({i+1}):")
        print(generate_whisper(sample_data_3))

    for i in range(3):
        print(f"\nWhisper for {sample_data_4['name']} ({i+1}):")
        print(generate_whisper(sample_data_4))

    print("\n--- Whisper generation test finished ---")
