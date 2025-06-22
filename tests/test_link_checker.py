import os
import tempfile
import unittest
from unittest.mock import patch

import scripts.link_checker as link_checker


class LinkCheckerTestCase(unittest.TestCase):
    def test_reports_missing_file(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            # create repo structure with .git to avoid fallback
            os.makedirs(os.path.join(tmpdir, '.git'))
            html_path = os.path.join(tmpdir, 'index.html')
            with open(html_path, 'w', encoding='utf-8') as f:
                f.write('<a href="missing.html">Broken</a>')
            fake_file = os.path.join(tmpdir, 'scripts', 'link_checker.py')
            os.makedirs(os.path.dirname(fake_file), exist_ok=True)
            # patch __file__ so repo_root resolves to tmpdir
            with patch.object(link_checker, '__file__', fake_file):
                with self.assertLogs(link_checker.logger, level='INFO') as cm:
                    link_checker.main()
            log_output = '\n'.join(cm.output)
            self.assertIn('/missing.html', log_output)


if __name__ == '__main__':
    unittest.main()
