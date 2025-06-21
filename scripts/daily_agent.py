import os
import subprocess
from pathlib import Path
import logging


def configure_logger() -> logging.Logger:
    """Return a logger configured once for this module."""
    logger = logging.getLogger(__name__)
    if not logger.handlers:
        logging.basicConfig(
            level=logging.INFO,
            format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
        )
    return logger


logger = configure_logger()

REPO_ROOT = Path(__file__).resolve().parents[1]


def run(cmd: str) -> bool:
    logger.info("Running: %s", cmd)
    result = subprocess.run(cmd, shell=True, cwd=REPO_ROOT)
    if result.returncode != 0:
        logger.error("Command failed with code %s: %s", result.returncode, cmd)
        return False
    return True


def main() -> None:
    success = True

    # Generate sitemap
    success &= run("python scripts/generate_sitemap.py")

    # Check internal links
    success &= run("python scripts/link_checker.py")

    # Optional database connectivity check
    if os.getenv("CONDADO_DB_PASSWORD"):
        success &= run("./scripts/check_db.sh")
    else:
        logger.info("CONDADO_DB_PASSWORD not set, skipping database check.")

    # Optional Gemini API test
    if os.getenv("GEMINI_API_KEY") or os.getenv("GeminiAPI"):
        run("bash scripts/gemini_request.sh")
    else:
        logger.info("GEMINI_API_KEY not set, skipping Gemini API test.")

    if not success:
        raise SystemExit(1)


if __name__ == "__main__":
    main()

