#!/bin/bash

# Job Placement System Frontend - Development Script
# This script helps with common development tasks

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
print_header() {
    echo -e "${BLUE}============================================${NC}"
    echo -e "${BLUE}  Job Placement System - Frontend Setup  ${NC}"
    echo -e "${BLUE}============================================${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Check if we're in the frontend directory
check_directory() {
    if [ ! -f "package.json" ]; then
        print_error "package.json not found. Please run this script from the frontend directory."
        exit 1
    fi
    
    if [ ! -f "next.config.js" ]; then
        print_error "next.config.js not found. This doesn't appear to be a Next.js project."
        exit 1
    fi
}

# Check Node.js version
check_node_version() {
    print_info "Checking Node.js version..."
    
    if ! command -v node &> /dev/null; then
        print_error "Node.js is not installed. Please install Node.js 16+ to continue."
        exit 1
    fi
    
    node_version=$(node -v | cut -d'v' -f2 | cut -d'.' -f1)
    if [ "$node_version" -lt 16 ]; then
        print_error "Node.js version $node_version is not supported. Please install Node.js 16+."
        exit 1
    fi
    
    print_success "Node.js version $(node -v) is compatible"
}

# Install dependencies
install_dependencies() {
    print_info "Installing dependencies..."
    
    if [ -f "package-lock.json" ]; then
        npm ci
    else
        npm install
    fi
    
    print_success "Dependencies installed successfully"
}

# Setup environment variables
setup_env() {
    print_info "Setting up environment variables..."
    
    if [ ! -f ".env.local" ]; then
        if [ -f ".env.example" ]; then
            cp .env.example .env.local
            print_success "Created .env.local from .env.example"
        else
            print_warning ".env.example not found. Creating basic .env.local"
            cat > .env.local << EOF
# Job Placement System Frontend Environment Variables
NEXT_PUBLIC_API_URL=http://localhost:3001/api
NEXT_PUBLIC_WHATSAPP_API_URL=http://localhost:3002
NEXT_PUBLIC_APP_NAME=Job Placement System
NEXT_PUBLIC_NODE_ENV=development
EOF
        fi
    else
        print_success ".env.local already exists"
    fi
}

# Type check
type_check() {
    print_info "Running TypeScript type check..."
    npm run type-check 2>/dev/null || npx tsc --noEmit
    print_success "TypeScript type check completed"
}

# Build the application
build_app() {
    print_info "Building application..."
    npm run build
    print_success "Application built successfully"
}

# Start development server
start_dev() {
    print_info "Starting development server..."
    print_info "The application will be available at http://localhost:3000"
    print_info "Press Ctrl+C to stop the server"
    echo ""
    npm run dev
}

# Start production server
start_prod() {
    print_info "Starting production server..."
    if [ ! -d ".next" ]; then
        print_warning "No build found. Building application first..."
        build_app
    fi
    
    print_info "The application will be available at http://localhost:3000"
    print_info "Press Ctrl+C to stop the server"
    echo ""
    npm start
}

# Run tests
run_tests() {
    print_info "Running tests..."
    if npm run test --silent 2>/dev/null; then
        npm run test
    else
        print_warning "No tests configured. Skipping test execution."
    fi
}

# Clean build artifacts
clean() {
    print_info "Cleaning build artifacts..."
    rm -rf .next
    rm -rf node_modules/.cache
    print_success "Build artifacts cleaned"
}

# Main menu
show_menu() {
    echo ""
    echo "Please select an option:"
    echo "1) Install dependencies"
    echo "2) Setup environment"
    echo "3) Type check"
    echo "4) Build application"
    echo "5) Start development server"
    echo "6) Start production server"
    echo "7) Run tests"
    echo "8) Clean build artifacts"
    echo "9) Full setup (1-4)"
    echo "0) Exit"
    echo ""
}

# Full setup
full_setup() {
    print_info "Running full setup..."
    install_dependencies
    setup_env
    type_check
    build_app
    print_success "Full setup completed successfully!"
    print_info "You can now run 'npm run dev' to start the development server"
}

# Main execution
main() {
    print_header
    check_directory
    check_node_version
    
    if [ $# -eq 0 ]; then
        # Interactive mode
        while true; do
            show_menu
            read -p "Enter your choice [0-9]: " choice
            
            case $choice in
                1)
                    install_dependencies
                    ;;
                2)
                    setup_env
                    ;;
                3)
                    type_check
                    ;;
                4)
                    build_app
                    ;;
                5)
                    start_dev
                    ;;
                6)
                    start_prod
                    ;;
                7)
                    run_tests
                    ;;
                8)
                    clean
                    ;;
                9)
                    full_setup
                    ;;
                0)
                    print_info "Goodbye!"
                    exit 0
                    ;;
                *)
                    print_error "Invalid option. Please try again."
                    ;;
            esac
            
            if [ "$choice" != "5" ] && [ "$choice" != "6" ]; then
                echo ""
                read -p "Press Enter to continue..."
            fi
        done
    else
        # Command line mode
        case $1 in
            install|deps)
                install_dependencies
                ;;
            setup|env)
                setup_env
                ;;
            check|type-check)
                type_check
                ;;
            build)
                build_app
                ;;
            dev|development)
                start_dev
                ;;
            start|prod|production)
                start_prod
                ;;
            test|tests)
                run_tests
                ;;
            clean)
                clean
                ;;
            full|setup-all)
                full_setup
                ;;
            *)
                echo "Usage: $0 [install|setup|check|build|dev|start|test|clean|full]"
                echo ""
                echo "Commands:"
                echo "  install    Install dependencies"
                echo "  setup      Setup environment variables"
                echo "  check      Run TypeScript type check"
                echo "  build      Build application"
                echo "  dev        Start development server"
                echo "  start      Start production server"
                echo "  test       Run tests"
                echo "  clean      Clean build artifacts"
                echo "  full       Run full setup"
                echo ""
                echo "Run without arguments for interactive mode."
                exit 1
                ;;
        esac
    fi
}

# Run main function
main "$@"
