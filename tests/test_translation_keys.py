import json
import unittest
from pathlib import Path

class TranslationKeysTest(unittest.TestCase):
    def test_all_files_have_same_keys(self):
        translation_dir = Path(__file__).resolve().parent.parent / 'translations'
        files = list(translation_dir.glob('*.json'))
        self.assertTrue(files, 'No translation files found')

        key_sets = {}
        all_keys = set()

        for file in files:
            with open(file, 'r', encoding='utf-8') as f:
                data = json.load(f)
            keys = set(data.keys())
            key_sets[file.name] = keys
            all_keys.update(keys)

        for name, keys in key_sets.items():
            missing = all_keys - keys
            self.assertFalse(missing, f'Missing keys in {name}: {", ".join(sorted(missing))}')

if __name__ == '__main__':
    unittest.main()
