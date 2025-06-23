#!/bin/bash
set -uo pipefail

# Attempt to install missing runtimes using apt-get when possible
install_pkg() {
    local pkg="$1"
    if dpkg -s "$pkg" >/dev/null 2>&1; then
        return
    fi
    echo "Installing $pkg..."
    if ! apt-get update -y >/dev/null 2>&1; then
        echo "Warning: apt-get update failed" >&2
        return 1
    fi
    if ! apt-get install -y "$pkg" >/dev/null 2>&1; then
        echo "Warning: failed to install $pkg" >&2
    fi
}

# Determine repository root and switch to it
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

# Ensure project root is on PYTHONPATH so modules can be imported in tests
export PYTHONPATH="$PROJECT_ROOT:${PYTHONPATH:-}"

# Required tool versions
REQUIRED_PHP_VERSION="8.1"
REQUIRED_NODE_VERSION="18"
REQUIRED_PYTHON_VERSION="3.10"

# Basic version check helper
check_version() {
    local cmd="$1"
    local required="$2"
    local name="$3"

    if ! command -v "$cmd" >/dev/null 2>&1; then
        echo "Error: $name $required or higher is required but not found." >&2
        return 1
    fi

    local current="$($cmd --version | head -n1 | grep -oE '[0-9]+(\.[0-9]+)+')"
    if [ "$(printf '%s\n' "$required" "$current" | sort -V | head -n1)" != "$required" ]; then
        echo "Error: $name $required or higher is required, but $current is installed." >&2
        return 1
    fi
}

# Check language runtimes
check_version php "$REQUIRED_PHP_VERSION" "PHP" || true
check_version node "$REQUIRED_NODE_VERSION" "Node.js" || true
check_version python3 "$REQUIRED_PYTHON_VERSION" "Python" || true

# Install runtimes if they are missing
if ! command -v php >/dev/null 2>&1; then
    install_pkg php-cli
    install_pkg php
fi

if ! command -v composer >/dev/null 2>&1; then
    if apt-get install -y composer >/dev/null 2>&1; then
        echo "Composer installed via apt"
    else
        echo "Downloading composer.phar..."
        if curl -sS https://getcomposer.org/installer | php >/dev/null 2>&1; then
            mv composer.phar /usr/local/bin/composer
            chmod +x /usr/local/bin/composer
        else
            echo "Warning: failed to install Composer" >&2
        fi
    fi
fi

if ! command -v node >/dev/null 2>&1; then
    echo "Node.js not found. Installing..."
    if curl -fsSL https://deb.nodesource.com/setup_${REQUIRED_NODE_VERSION}.x | bash - >/dev/null 2>&1 && \
       apt-get install -y nodejs >/dev/null 2>&1; then
        echo "Node.js installed"
    else
        echo "Warning: failed to install Node.js" >&2
    fi
fi

if ! command -v python3 >/dev/null 2>&1; then
    install_pkg python3
fi

if ! command -v pip >/dev/null 2>&1; then
    install_pkg python3-pip
fi

# Verify Composer is available before proceeding
if ! command -v composer >/dev/null 2>&1; then
    echo "Error: Composer is required to run this script. Please install it first." >&2
    exit 1
fi

# Install PHP dependencies
if ! composer install --no-interaction --no-progress --prefer-dist; then
    echo "Warning: composer install failed." >&2
fi

# Install Python dependencies
if command -v pip >/dev/null 2>&1; then
    PYTHON_DEPS_DIR="vendor/python-deps"
    PIP_EXTRA_ARGS=""
    if [ -d "$PYTHON_DEPS_DIR" ] && ls "$PYTHON_DEPS_DIR"/*.whl "$PYTHON_DEPS_DIR"/*.tar.gz >/dev/null 2>&1; then
        PIP_EXTRA_ARGS="--no-index --find-links $PYTHON_DEPS_DIR"
    fi
    if ! pip install $PIP_EXTRA_ARGS -r requirements.txt; then
        echo "Warning: pip install failed." >&2
    fi
else
    echo "Warning: pip not found; skipping Python dependencies." >&2
fi

# Install Node dependencies
if command -v npm >/dev/null 2>&1; then
    if ! npm ci; then
        echo "Warning: npm install failed." >&2
    fi
else
    echo "Warning: npm not found; skipping Node dependencies." >&2
fi

cat <<MSG
Environment setup completed.
MSG

