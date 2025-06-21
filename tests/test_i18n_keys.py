import json
import unittest
from pathlib import Path


class I18nKeysTest(unittest.TestCase):
    def test_i18n_and_translation_keys_match(self):
        project_root = Path(__file__).resolve().parent.parent
        base_file = project_root / 'i18n' / 'es.json'
        translation_dir = project_root / 'translations'

        files = [base_file] + list(translation_dir.glob('*.json'))
        self.assertTrue(files, 'No translation files found')

        key_sets = []
        for fpath in files:
            with open(fpath, 'r', encoding='utf-8') as f:
                data = json.load(f)
            key_sets.append((fpath.name, set(data.keys())))

        base_keys = key_sets[0][1]
        for name, keys in key_sets[1:]:
            self.assertEqual(base_keys, keys, f'Key mismatch in {name}')


if __name__ == '__main__':
    unittest.main()
