import dayjs from 'dayjs';
import 'dayjs/locale/id';
import relativeTime from 'dayjs/plugin/relativeTime';
import utc from 'dayjs/plugin/utc';
import timezone from 'dayjs/plugin/timezone';

// Setup dayjs
dayjs.extend(relativeTime);
dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.locale('id');

// Format dates
export const formatDate = (date: string | Date, format: string = 'DD MMMM YYYY') => {
  return dayjs(date).format(format);
};

export const formatDateTime = (date: string | Date) => {
  return dayjs(date).format('DD MMMM YYYY HH:mm');
};

export const formatTime = (date: string | Date) => {
  return dayjs(date).format('HH:mm');
};

export const formatRelativeTime = (date: string | Date) => {
  return dayjs(date).fromNow();
};

// Format numbers
export const formatNumber = (num: number) => {
  return new Intl.NumberFormat('id-ID').format(num);
};

export const formatCurrency = (amount: number, currency: string = 'IDR') => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: currency,
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
};

export const formatPercentage = (value: number, decimals: number = 1) => {
  return `${value.toFixed(decimals)}%`;
};

// Format phone numbers
export const formatPhoneNumber = (phone: string) => {
  // Remove all non-digits
  const cleaned = phone.replace(/\D/g, '');
  
  // Format Indonesian phone number
  if (cleaned.startsWith('62')) {
    return `+${cleaned}`;
  } else if (cleaned.startsWith('0')) {
    return `+62${cleaned.substring(1)}`;
  } else {
    return `+62${cleaned}`;
  }
};

// Validate phone numbers
export const isValidPhoneNumber = (phone: string) => {
  const phoneRegex = /^(\+62|62|0)[0-9]{9,12}$/;
  return phoneRegex.test(phone.replace(/\s/g, ''));
};

// Validate email
export const isValidEmail = (email: string) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
};

// Format file size
export const formatFileSize = (bytes: number) => {
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  if (bytes === 0) return '0 Bytes';
  const i = Math.floor(Math.log(bytes) / Math.log(1024));
  return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
};

// Generate random string
export const generateRandomString = (length: number = 8) => {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let result = '';
  for (let i = 0; i < length; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return result;
};

// Capitalize first letter
export const capitalize = (str: string) => {
  return str.charAt(0).toUpperCase() + str.slice(1);
};

// Convert snake_case to Title Case
export const snakeToTitle = (str: string) => {
  return str
    .split('_')
    .map(word => capitalize(word))
    .join(' ');
};

// Truncate text
export const truncateText = (text: string, maxLength: number = 100) => {
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
};

// Download file
export const downloadFile = (data: Blob, filename: string) => {
  const url = window.URL.createObjectURL(data);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  window.URL.revokeObjectURL(url);
  document.body.removeChild(a);
};

// Copy to clipboard
export const copyToClipboard = async (text: string) => {
  try {
    await navigator.clipboard.writeText(text);
    return true;
  } catch (err) {
    console.error('Failed to copy: ', err);
    return false;
  }
};

// Get initials from name
export const getInitials = (name: string) => {
  return name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .substring(0, 2);
};

// Get color by index (for charts, avatars, etc.)
export const getColorByIndex = (index: number) => {
  const colors = [
    '#1890ff', '#52c41a', '#faad14', '#ff4d4f', '#722ed1',
    '#13c2c2', '#eb2f96', '#f5222d', '#fa541c', '#fa8c16',
    '#a0d911', '#52c41a', '#13c2c2', '#1890ff', '#2f54eb',
    '#722ed1', '#eb2f96', '#f5222d'
  ];
  return colors[index % colors.length];
};

// Debounce function
export const debounce = <T extends (...args: any[]) => any>(
  func: T,
  wait: number
): ((...args: Parameters<T>) => void) => {
  let timeout: NodeJS.Timeout;
  return (...args: Parameters<T>) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => func(...args), wait);
  };
};

// Local storage helpers
export const storage = {
  set: (key: string, value: any) => {
    try {
      localStorage.setItem(key, JSON.stringify(value));
    } catch (error) {
      console.error('Error saving to localStorage:', error);
    }
  },
  get: <T = any>(key: string): T | null => {
    try {
      const item = localStorage.getItem(key);
      return item ? JSON.parse(item) : null;
    } catch (error) {
      console.error('Error reading from localStorage:', error);
      return null;
    }
  },
  remove: (key: string) => {
    try {
      localStorage.removeItem(key);
    } catch (error) {
      console.error('Error removing from localStorage:', error);
    }
  },
  clear: () => {
    try {
      localStorage.clear();
    } catch (error) {
      console.error('Error clearing localStorage:', error);
    }
  }
};

// URL helpers
export const buildQueryString = (params: Record<string, any>) => {
  const searchParams = new URLSearchParams();
  Object.entries(params).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== '') {
      searchParams.append(key, String(value));
    }
  });
  return searchParams.toString();
};

// Status helpers
export const getStatusColor = (status: string) => {
  const statusColors: Record<string, string> = {
    // Application statuses
    'applied': '#1890ff',
    'screening': '#faad14',
    'psikotes': '#722ed1',
    'interview': '#13c2c2',
    'medical': '#fa8c16',
    'accepted': '#52c41a',
    'rejected': '#ff4d4f',
    'withdrawn': '#8c8c8c',
    
    // Job statuses
    'active': '#52c41a',
    'inactive': '#ff4d4f',
    'draft': '#faad14',
    'closed': '#8c8c8c',
    
    // Placement statuses
    'working': '#52c41a',
    'completed': '#1890ff',
    'terminated': '#ff4d4f',
    'resigned': '#faad14',
    
    // WhatsApp statuses
    'sent': '#1890ff',
    'delivered': '#52c41a',
    'read': '#722ed1',
    'failed': '#ff4d4f',
    'pending': '#faad14',
    
    // General statuses
    'pending': '#faad14',
    'approved': '#52c41a',
    'declined': '#ff4d4f',
    'cancelled': '#8c8c8c',
  };
  
  return statusColors[status.toLowerCase()] || '#8c8c8c';
};

export const getStatusText = (status: string) => {
  const statusTexts: Record<string, string> = {
    // Application statuses
    'applied': 'Melamar',
    'screening': 'Screening',
    'psikotes': 'Psikotes',
    'interview': 'Interview',
    'medical': 'Medical Check-up',
    'accepted': 'Diterima',
    'rejected': 'Ditolak',
    'withdrawn': 'Dibatalkan',
    
    // Job statuses
    'active': 'Aktif',
    'inactive': 'Tidak Aktif',
    'draft': 'Draft',
    'closed': 'Ditutup',
    
    // Placement statuses
    'working': 'Bekerja',
    'completed': 'Selesai',
    'terminated': 'Di-PHK',
    'resigned': 'Mengundurkan Diri',
    
    // WhatsApp statuses
    'sent': 'Terkirim',
    'delivered': 'Sampai',
    'read': 'Dibaca',
    'failed': 'Gagal',
    'pending': 'Menunggu',
    
    // General statuses
    'pending': 'Menunggu',
    'approved': 'Disetujui',
    'declined': 'Ditolak',
    'cancelled': 'Dibatalkan',
  };
  
  return statusTexts[status.toLowerCase()] || status;
};

// Array helpers
export const groupBy = <T>(array: T[], key: keyof T) => {
  return array.reduce((groups, item) => {
    const groupKey = String(item[key]);
    if (!groups[groupKey]) {
      groups[groupKey] = [];
    }
    groups[groupKey].push(item);
    return groups;
  }, {} as Record<string, T[]>);
};

export const sortBy = <T>(array: T[], key: keyof T, direction: 'asc' | 'desc' = 'asc') => {
  return [...array].sort((a, b) => {
    const aVal = a[key];
    const bVal = b[key];
    
    if (aVal < bVal) return direction === 'asc' ? -1 : 1;
    if (aVal > bVal) return direction === 'asc' ? 1 : -1;
    return 0;
  });
};

// Form validation helpers
export const validateRequired = (value: any, message: string = 'Field ini wajib diisi') => {
  if (!value || (typeof value === 'string' && value.trim() === '')) {
    return message;
  }
  return undefined;
};

export const validateEmail = (email: string) => {
  if (!email) return 'Email wajib diisi';
  if (!isValidEmail(email)) return 'Format email tidak valid';
  return undefined;
};

export const validatePhone = (phone: string) => {
  if (!phone) return 'Nomor telepon wajib diisi';
  if (!isValidPhoneNumber(phone)) return 'Format nomor telepon tidak valid';
  return undefined;
};

export const validateMinLength = (value: string, minLength: number) => {
  if (!value) return undefined;
  if (value.length < minLength) return `Minimal ${minLength} karakter`;
  return undefined;
};

export const validateMaxLength = (value: string, maxLength: number) => {
  if (!value) return undefined;
  if (value.length > maxLength) return `Maksimal ${maxLength} karakter`;
  return undefined;
};
