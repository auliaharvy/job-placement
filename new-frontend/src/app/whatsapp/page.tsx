'use client';

import React, { useState } from 'react';
import DashboardLayout from '@/components/DashboardLayout';
import { 
  MessageSquare, 
  Send, 
  Users, 
  CheckCircle, 
  XCircle, 
  Clock, 
  Smartphone,
  MessageCircle,
  Bell,
  Settings,
  Activity,
  TrendingUp,
  Download,
  Filter,
  Search,
  Plus
} from 'lucide-react';

export default function WhatsAppPage() {
  const [connectionStatus] = useState<'connected' | 'disconnected' | 'connecting'>('connected');
  
  const stats = {
    totalMessages: 1247,
    delivered: 1182,
    failed: 23,
    pending: 42,
    activeContacts: 456,
    todayMessages: 89
  };

  const recentMessages = [
    {
      id: 1,
      recipient: 'John Doe (+62812345678)',
      message: 'Selamat! Anda telah lolos tahap interview untuk posisi Full Stack Developer...',
      status: 'delivered',
      timestamp: '2025-06-07 14:30',
      type: 'selection_update'
    },
    {
      id: 2,
      recipient: 'Jane Smith (+62823456789)', 
      message: 'Lowongan baru: Marketing Manager di CV Berkah Mandiri. Tertarik? Klik link...',
      status: 'delivered',
      timestamp: '2025-06-07 13:15',
      type: 'job_broadcast'
    },
    {
      id: 3,
      recipient: 'Ahmad Rahman (+62834567890)',
      message: 'Selamat datang di Job Placement System! Terima kasih telah mendaftar...',
      status: 'failed',
      timestamp: '2025-06-07 12:45',
      type: 'welcome_message'
    },
    {
      id: 4,
      recipient: 'Siti Nurhaliza (+62845678901)',
      message: 'Reminder: Kontrak kerja Anda akan berakhir dalam 30 hari. Silakan...',
      status: 'pending',
      timestamp: '2025-06-07 11:20',
      type: 'contract_reminder'
    }
  ];

  const messageTypes = [
    { id: 'welcome_message', label: 'Pesan Selamat Datang', count: 145 },
    { id: 'job_broadcast', label: 'Broadcast Lowongan', count: 89 },
    { id: 'selection_update', label: 'Update Seleksi', count: 67 },
    { id: 'contract_reminder', label: 'Pengingat Kontrak', count: 23 },
    { id: 'interview_schedule', label: 'Jadwal Interview', count: 45 }
  ];

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'delivered': return 'text-green-600 bg-green-100';
      case 'failed': return 'text-red-600 bg-red-100';
      case 'pending': return 'text-yellow-600 bg-yellow-100';
      default: return 'text-gray-600 bg-gray-100';
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'delivered': return CheckCircle;
      case 'failed': return XCircle;
      case 'pending': return Clock;
      default: return MessageCircle;
    }
  };

  return (
    <DashboardLayout>
      <div className="mb-8">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">WhatsApp Management</h1>
            <p className="text-gray-600 mt-1">Kelola integrasi WhatsApp dan kirim notifikasi otomatis</p>
          </div>
          <div className="flex space-x-3">
            <button className="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
              <Download className="w-4 h-4 mr-2" />
              Export Log
            </button>
            <button className="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
              <Settings className="w-4 h-4 mr-2" />
              Pengaturan
            </button>
            <button className="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
              <Send className="w-4 h-4 mr-2" />
              Kirim Pesan
            </button>
          </div>
        </div>

        {/* Connection Status */}
        <div className="bg-white border border-gray-200 rounded-lg p-6 mb-6">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-4">
              <div className={`w-12 h-12 rounded-full flex items-center justify-center ${
                connectionStatus === 'connected' ? 'bg-green-100' : 
                connectionStatus === 'connecting' ? 'bg-yellow-100' : 'bg-red-100'
              }`}>
                <Smartphone className={`w-6 h-6 ${
                  connectionStatus === 'connected' ? 'text-green-600' : 
                  connectionStatus === 'connecting' ? 'text-yellow-600' : 'text-red-600'
                }`} />
              </div>
              <div>
                <h3 className="text-lg font-semibold text-gray-900">
                  Status Koneksi WhatsApp
                </h3>
                <p className={`text-sm ${
                  connectionStatus === 'connected' ? 'text-green-600' : 
                  connectionStatus === 'connecting' ? 'text-yellow-600' : 'text-red-600'
                }`}>
                  {connectionStatus === 'connected' ? 'Terhubung' : 
                   connectionStatus === 'connecting' ? 'Menghubungkan...' : 'Terputus'}
                </p>
              </div>
            </div>
            <div className="text-right">
              <p className="text-sm text-gray-600">Gateway: wa_gateway</p>
              <p className="text-sm text-gray-600">Port: 3001</p>
            </div>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Total Pesan</p>
                <p className="text-2xl font-bold text-gray-900">{stats.totalMessages}</p>
              </div>
              <MessageSquare className="w-8 h-8 text-blue-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Terkirim</p>
                <p className="text-2xl font-bold text-green-600">{stats.delivered}</p>
              </div>
              <CheckCircle className="w-8 h-8 text-green-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Gagal</p>
                <p className="text-2xl font-bold text-red-600">{stats.failed}</p>
              </div>
              <XCircle className="w-8 h-8 text-red-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Pending</p>
                <p className="text-2xl font-bold text-yellow-600">{stats.pending}</p>
              </div>
              <Clock className="w-8 h-8 text-yellow-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Kontak Aktif</p>
                <p className="text-2xl font-bold text-purple-600">{stats.activeContacts}</p>
              </div>
              <Users className="w-8 h-8 text-purple-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Hari Ini</p>
                <p className="text-2xl font-bold text-orange-600">{stats.todayMessages}</p>
              </div>
              <TrendingUp className="w-8 h-8 text-orange-600" />
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Message Types */}
          <div className="bg-white border border-gray-200 rounded-lg p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">Tipe Pesan</h3>
            <div className="space-y-3">
              {messageTypes.map((type) => (
                <div key={type.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div className="flex items-center space-x-3">
                    <MessageCircle className="w-4 h-4 text-blue-600" />
                    <span className="text-sm font-medium text-gray-900">{type.label}</span>
                  </div>
                  <span className="text-sm font-semibold text-blue-600">{type.count}</span>
                </div>
              ))}
            </div>
          </div>

          {/* Recent Messages */}
          <div className="bg-white border border-gray-200 rounded-lg p-6">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-lg font-semibold text-gray-900">Pesan Terbaru</h3>
              <button className="text-sm text-blue-600 hover:text-blue-700">Lihat Semua</button>
            </div>
            <div className="space-y-4">
              {recentMessages.map((message) => {
                const StatusIcon = getStatusIcon(message.status);
                return (
                  <div key={message.id} className="border border-gray-100 rounded-lg p-4">
                    <div className="flex items-start justify-between mb-2">
                      <div className="flex items-center space-x-2">
                        <span className="text-sm font-medium text-gray-900">{message.recipient}</span>
                        <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${getStatusColor(message.status)}`}>
                          <StatusIcon className="w-3 h-3 mr-1" />
                          {message.status}
                        </span>
                      </div>
                      <span className="text-xs text-gray-500">{message.timestamp}</span>
                    </div>
                    <p className="text-sm text-gray-600 mb-2 line-clamp-2">{message.message}</p>
                    <span className="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700">
                      {message.type.replace('_', ' ')}
                    </span>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}
