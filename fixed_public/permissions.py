#!/usr/bin/env python3
"""
setup_permissions.py
Purpose: Set up directory structure and permissions for TOR-enabled website
"""

import os
import subprocess
import sys
from pathlib import Path

def run_command(command):
    """Execute shell command and print output"""
    try:
        result = subprocess.run(command, shell=True, check=True, 
                              capture_output=True, text=True)
        print(f"Success: {command}")
        return True
    except subprocess.CalledProcessError as e:
        print(f"Error executing: {command}")
        print(f"Error message: {e.stderr}")
        return False

def create_directory_structure(base_path):
    """Create the directory structure"""
    directories = [
        'public/assets/css',
        'public/assets/images',
        'handlers',
        'includes',
        'cache',
        'logs',
        'data/submissions'
    ]
    
    # Create directories
    for dir_path in directories:
        full_path = os.path.join(base_path, dir_path)
        try:
            Path(full_path).mkdir(parents=True, exist_ok=True)
            print(f"Created directory: {full_path}")
        except Exception as e:
            print(f"Error creating directory {full_path}: {e}")
            return False
    return True

def create_files(base_path):
    """Create necessary files"""
    files = [
        'public/assets/css/styles.css',
        'logs/error.log',
        'logs/access.log',
        'data/submissions/newsletter.txt',
        'data/submissions/contact.txt'
    ]
    
    # Create files
    for file_path in files:
        full_path = os.path.join(base_path, file_path)
        try:
            Path(full_path).touch()
            print(f"Created file: {full_path}")
        except Exception as e:
            print(f"Error creating file {full_path}: {e}")
            return False
    return True

def set_permissions(base_path):
    """Set proper permissions for directories and files"""
    # Directory permissions
    dir_permissions = [
        ('755', [
            '.',
            'public',
            'public/assets',
            'public/assets/css',
            'public/assets/images',
            'handlers',
            'includes'
        ]),
        ('775', [
            'cache',
            'logs',
            'data/submissions'
        ])
    ]
    
    # File permissions
    file_permissions = [
        ('644', [
            'public/assets/css/styles.css'
        ]),
        ('666', [
            'logs/error.log',
            'logs/access.log',
            'data/submissions/newsletter.txt',
            'data/submissions/contact.txt'
        ])
    ]
    
    # Set directory permissions
    for perm, dirs in dir_permissions:
        for dir_path in dirs:
            full_path = os.path.join(base_path, dir_path)
            if not run_command(f"chmod {perm} {full_path}"):
                return False
    
    # Set file permissions
    for perm, files in file_permissions:
        for file_path in files:
            full_path = os.path.join(base_path, file_path)
            if not run_command(f"chmod {perm} {full_path}"):
                return False
    
    return True

def set_ownership(base_path, user="www-data", group="www-data"):
    """Set ownership of all files and directories"""
    return run_command(f"chown -R {user}:{group} {base_path}")

def main():
    # Check if running as root
    if os.geteuid() != 0:
        print("This script must be run as root (use sudo)")
        sys.exit(1)
    
    # Get base path from argument or use default
    base_path = sys.argv[1] if len(sys.argv) > 1 else "/var/www/html"
    
    print(f"Setting up directory structure in: {base_path}")
    
    # Create directory structure
    if not create_directory_structure(base_path):
        sys.exit(1)
    
    # Create necessary files
    if not create_files(base_path):
        sys.exit(1)
    
    # Set permissions
    if not set_permissions(base_path):
        sys.exit(1)
    
    # Set ownership
    if not set_ownership(base_path):
        sys.exit(1)
    
    print("\nSetup completed successfully!")
    print("\nDirectory structure created:")
    os.system(f"tree {base_path}")

if __name__ == "__main__":
    main()
