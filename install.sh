#!/bin/bash

# BoardRenewal - Quick Install Script
# This script helps you install BoardRenewal theme for Kanboard

set -e

echo "🎨 BoardRenewal Theme Installer"
echo "================================"
echo ""

# Check if we're in the right directory
if [ ! -f "Plugin.php" ]; then
    echo "❌ Error: Plugin.php not found. Please run this script from the BoardRenewal directory."
    exit 1
fi

# Detect Kanboard installation
KANBOARD_PATHS=(
    "/var/www/html"
    "/var/www/kanboard"
    "/opt/kanboard"
    "/usr/share/kanboard"
    "$HOME/kanboard"
    "$HOME/www/kanboard"
)

echo "🔍 Searching for Kanboard installation..."

KANBOARD_DIR=""
for path in "${KANBOARD_PATHS[@]}"; do
    if [ -f "$path/kanboard" ] || [ -f "$path/index.php" ]; then
        KANBOARD_DIR="$path"
        break
    fi
done

if [ -z "$KANBOARD_DIR" ]; then
    echo "❌ Could not find Kanboard installation."
    echo ""
    echo "Please manually copy the BoardRenewal folder to:"
    echo "  <kanboard-root>/plugins/BoardRenewal/"
    echo ""
    exit 1
fi

echo "✅ Found Kanboard at: $KANBOARD_DIR"
echo ""

# Check if plugins directory exists
if [ ! -d "$KANBOARD_DIR/plugins" ]; then
    echo "📁 Creating plugins directory..."
    mkdir -p "$KANBOARD_DIR/plugins"
fi

# Copy plugin
echo "📦 Installing BoardRenewal..."
DEST="$KANBOARD_DIR/plugins/BoardRenewal"

if [ -d "$DEST" ]; then
    echo "⚠️  BoardRenewal is already installed at: $DEST"
    read -p "Do you want to overwrite? (y/N) " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "Installation cancelled."
        exit 0
    fi
    rm -rf "$DEST"
fi

cp -r . "$DEST"

echo ""
echo "✅ BoardRenewal installed successfully!"
echo ""
echo "📍 Location: $DEST"
echo ""
echo "🔧 Next steps:"
echo "   1. Log in to Kanboard as administrator"
echo "   2. Go to Settings > Plugins"
echo "   3. BoardRenewal should appear in the list"
echo ""
echo "🎉 Enjoy your new modern theme!"
echo ""
