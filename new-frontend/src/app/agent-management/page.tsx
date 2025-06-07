'use client';

import React, { useState } from 'react';
import DashboardLayout from '@/components/DashboardLayout';
import { 
  UserCheck, 
  Search, 
  Filter, 
  Plus, 
  MoreVertical,
  Eye,
  Edit,
  Trash2,
  Download,
  Upload,
  MapPin,
  Phone,
  Mail,
  Users,
  TrendingUp,
  Award,
  DollarSign,
  Calendar,
  CheckCircle,
  XCircle,
  Clock,
  Star,
  Target,
  QrCode
} from 'lucide-react';

interface Agent {
  id: number;
  agent_code: string;
  user: {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone?: string;
  };
  level: 'bronze' | 'silver' | 'gold' | 'platinum' | 'diamond';
  status: 'active' | 'inactive' | 'suspended';
  referral_code: string;
  total_referrals: number;
  successful_placements: number;
  success_rate: number;
  total_commission: number;
  total_points: number;
  qr_code_url: string;
  created_at: string;
  last_activity: string;
  target_monthly: number;
  achievement_current_month: number;
}

const statusConfig = {
  active: { 
    label: 'Aktif', 
    color: 'bg-green-100 text-green-800', 
    icon: CheckCircle 
  },
  inactive: { 
    label: 'Tidak Aktif', 
    color: 'bg-gray-100 text-gray-800', 
    icon: XCircle 
  },
  suspended: { 
    label: 'Disuspen', 
    color: 'bg-red-100 text-red-800', 
    icon: Clock 
  }
};

const levelConfig = {
  bronze: { label: 'Bronze', color: 'bg-amber-100 text-amber-800', bgColor: 'bg-amber-50' },
  silver: { label: 'Silver', color: 'bg-gray-100 text-gray-800', bgColor: 'bg-gray-50' },
  gold: { label: 'Gold', color: 'bg-yellow-100 text-yellow-800', bgColor: 'bg-yellow-50' },
  platinum: { label: 'Platinum', color: 'bg-blue-100 text-blue-800', bgColor: 'bg-blue-50' },
  diamond: { label: 'Diamond', color: 'bg-purple-100 text-purple-800', bgColor: 'bg-purple-50' }
};

export default function AgentManagementPage() {
  const [agents, setAgents] = useState<Agent[]>([
    {
      id: 1,
      agent_code: 'AGT001',
      user: {
        id: 1,
        first_name: 'John',
        last_name: 'Doe',
        email: 'john.doe@email.com',
        phone: '+62812345678'
      },
      level: 'gold',
      status: 'active',
      referral_code: 'JOHN001',
      total_referrals: 45,
      successful_placements: 32,
      success_rate: 71.1,
      total_commission: 25000000,
      total_points: 1250,
      qr_code_url: 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=JOHN001',
      created_at: '2024-01-15T10:00:00Z',
      last_activity: '2025-06-07T14:30:00Z',
      target_monthly: 10,
      achievement_current_month: 8
    },
    {
      id: 2,
      agent_code: 'AGT002',
      user: {
        id: 2,
        first_name: 'Jane',
        last_name: 'Smith',
        email: 'jane.smith@email.com',
        phone: '+62823456789'
      },
      level: 'silver',
      status: 'active',
      referral_code: 'JANE002',
      total_referrals: 28,
      successful_placements: 20,
      success_rate: 71.4,
      total_commission: 15000000,
      total_points: 750,
      qr_code_url: 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=JANE002',
      created_at: '2024-02-20T14:30:00Z',
      last_activity: '2025-06-06T09:15:00Z',
      target_monthly: 8,
      achievement_current_month: 6
    },
    {
      id: 3,
      agent_code: 'AGT003',
      user: {
        id: 3,
        first_name: 'Ahmad',
        last_name: 'Rahman',
        email: 'ahmad.rahman@email.com',
        phone: '+62834567890'
      },
      level: 'bronze',
      status: 'active',
      referral_code: 'AHMAD003',
      total_referrals: 12,
      successful_placements: 8,
      success_rate: 66.7,
      total_commission: 5000000,
      total_points: 250,
      qr_code_url: 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=AHMAD003',
      created_at: '2024-11-10T16:20:00Z',
      last_activity: '2025-06-05T11:45:00Z',
      target_monthly: 5,
      achievement_current_month: 2
    },
    {
      id: 4,
      agent_code: 'AGT004',
      user: {
        id: 4,
        first_name: 'Siti',
        last_name: 'Nurhaliza',
        email: 'siti.nurhaliza@email.com',
        phone: '+62845678901'
      },
      level: 'platinum',
      status: 'inactive',
      referral_code: 'SITI004',
      total_referrals: 78,
      successful_placements: 65,
      success_rate: 83.3,
      total_commission: 45000000,
      total_points: 2250,
      qr_code_url: 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=SITI004',
      created_at: '2023-08-05T12:00:00Z',
      last_activity: '2025-05-20T16:00:00Z',
      target_monthly: 15,
      achievement_current_month: 0
    }
  ]);

  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [levelFilter, setLevelFilter] = useState('all');

  const filteredAgents = agents.filter(agent => {
    const matchesSearch = searchTerm === '' || 
      agent.user.first_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      agent.user.last_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      agent.user.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
      agent.agent_code.toLowerCase().includes(searchTerm.toLowerCase()) ||
      agent.referral_code.toLowerCase().includes(searchTerm.toLowerCase());
    
    const matchesStatus = statusFilter === 'all' || agent.status === statusFilter;
    const matchesLevel = levelFilter === 'all' || agent.level === levelFilter;
    
    return matchesSearch && matchesStatus && matchesLevel;
  });

  const stats = {
    total: agents.length,
    active: agents.filter(a => a.status === 'active').length,
    inactive: agents.filter(a => a.status === 'inactive').length,
    suspended: agents.filter(a => a.status === 'suspended').length,
    totalReferrals: agents.reduce((sum, a) => sum + a.total_referrals, 0),
    totalPlacements: agents.reduce((sum, a) => sum + a.successful_placements, 0),
    totalCommission: agents.reduce((sum, a) => sum + a.total_commission, 0),
    avgSuccessRate: agents.length > 0 ? agents.reduce((sum, a) => sum + a.success_rate, 0) / agents.length : 0
  };

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(amount);
  };

  const AgentCard = ({ agent }: { agent: Agent }) => {
    const StatusIcon = statusConfig[agent.status].icon;
    const progressPercentage = (agent.achievement_current_month / agent.target_monthly) * 100;
    
    return (
      <div className="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
        <div className="flex items-start justify-between mb-4">
          <div className="flex items-center space-x-3">
            <div className={`w-12 h-12 rounded-full flex items-center justify-center ${levelConfig[agent.level].bgColor}`}>
              <UserCheck className="w-6 h-6 text-blue-600" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">
                {agent.user.first_name} {agent.user.last_name}
              </h3>
              <p className="text-sm text-gray-500">{agent.agent_code}</p>
            </div>
          </div>
          <div className="flex items-center space-x-2">
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusConfig[agent.status].color}`}>
              <StatusIcon className="w-3 h-3 mr-1" />
              {statusConfig[agent.status].label}
            </span>
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${levelConfig[agent.level].color}`}>
              <Award className="w-3 h-3 mr-1" />
              {levelConfig[agent.level].label}
            </span>
            <button className="p-1 hover:bg-gray-100 rounded">
              <MoreVertical className="w-4 h-4 text-gray-400" />
            </button>
          </div>
        </div>

        <div className="space-y-2 mb-4">
          <div className="flex items-center text-sm text-gray-600">
            <Mail className="w-4 h-4 mr-2" />
            {agent.user.email}
          </div>
          {agent.user.phone && (
            <div className="flex items-center text-sm text-gray-600">
              <Phone className="w-4 h-4 mr-2" />
              {agent.user.phone}
            </div>
          )}
          <div className="flex items-center text-sm text-gray-600">
            <QrCode className="w-4 h-4 mr-2" />
            Kode Referral: <span className="font-mono ml-1">{agent.referral_code}</span>
          </div>
        </div>

        {/* Performance Stats */}
        <div className="bg-gray-50 p-4 rounded-lg mb-4">
          <div className="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span className="text-gray-600">Total Referral:</span>
              <span className="ml-2 font-semibold text-blue-600">{agent.total_referrals}</span>
            </div>
            <div>
              <span className="text-gray-600">Berhasil:</span>
              <span className="ml-2 font-semibold text-green-600">{agent.successful_placements}</span>
            </div>
            <div>
              <span className="text-gray-600">Success Rate:</span>
              <span className="ml-2 font-semibold text-purple-600">{agent.success_rate.toFixed(1)}%</span>
            </div>
            <div>
              <span className="text-gray-600">Points:</span>
              <span className="ml-2 font-semibold text-orange-600">{agent.total_points}</span>
            </div>
          </div>
        </div>

        {/* Monthly Target Progress */}
        <div className="mb-4">
          <div className="flex items-center justify-between text-sm mb-2">
            <span className="text-gray-600">Target Bulanan</span>
            <span className="font-medium">
              {agent.achievement_current_month} / {agent.target_monthly}
            </span>
          </div>
          <div className="w-full bg-gray-200 rounded-full h-2">
            <div 
              className="bg-blue-600 h-2 rounded-full transition-all duration-300" 
              style={{ width: `${Math.min(progressPercentage, 100)}%` }}
            ></div>
          </div>
          <p className="text-xs text-gray-500 mt-1">
            {progressPercentage >= 100 ? '✅ Target tercapai!' : `${progressPercentage.toFixed(0)}% dari target`}
          </p>
        </div>

        {/* Commission */}
        <div className="bg-green-50 p-3 rounded-lg mb-4">
          <div className="flex items-center text-sm">
            <DollarSign className="w-4 h-4 text-green-600 mr-2" />
            <span className="text-green-800">
              Total Komisi: <span className="font-semibold">{formatCurrency(agent.total_commission)}</span>
            </span>
          </div>
        </div>

        <div className="flex space-x-2">
          <button className="flex-1 inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100">
            <Eye className="w-3 h-3 mr-1" />
            Detail
          </button>
          <button className="flex-1 inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-md hover:bg-gray-100">
            <Edit className="w-3 h-3 mr-1" />
            Edit
          </button>
          <button className="flex-1 inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 rounded-md hover:bg-green-100">
            <QrCode className="w-3 h-3 mr-1" />
            QR Code
          </button>
        </div>
      </div>
    );
  };

  return (
    <DashboardLayout>
      <div className="mb-8">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Manajemen Agent</h1>
            <p className="text-gray-600 mt-1">Kelola agent dan track performa referral</p>
          </div>
          <div className="flex space-x-3">
            <button className="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
              <Upload className="w-4 h-4 mr-2" />
              Import
            </button>
            <button className="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
              <Download className="w-4 h-4 mr-2" />
              Export
            </button>
            <button className="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
              <Plus className="w-4 h-4 mr-2" />
              Tambah Agent
            </button>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-8 gap-4 mb-6">
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Total Agent</p>
                <p className="text-2xl font-bold text-gray-900">{stats.total}</p>
              </div>
              <UserCheck className="w-8 h-8 text-blue-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Aktif</p>
                <p className="text-2xl font-bold text-green-600">{stats.active}</p>
              </div>
              <CheckCircle className="w-8 h-8 text-green-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Tidak Aktif</p>
                <p className="text-2xl font-bold text-gray-600">{stats.inactive}</p>
              </div>
              <XCircle className="w-8 h-8 text-gray-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Disuspen</p>
                <p className="text-2xl font-bold text-red-600">{stats.suspended}</p>
              </div>
              <Clock className="w-8 h-8 text-red-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Total Referral</p>
                <p className="text-2xl font-bold text-purple-600">{stats.totalReferrals}</p>
              </div>
              <Users className="w-8 h-8 text-purple-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Penempatan</p>
                <p className="text-2xl font-bold text-orange-600">{stats.totalPlacements}</p>
              </div>
              <Target className="w-8 h-8 text-orange-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Avg Success</p>
                <p className="text-2xl font-bold text-green-600">{stats.avgSuccessRate.toFixed(1)}%</p>
              </div>
              <TrendingUp className="w-8 h-8 text-green-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Total Komisi</p>
                <p className="text-lg font-bold text-blue-600">{(stats.totalCommission / 1000000).toFixed(0)}M</p>
              </div>
              <DollarSign className="w-8 h-8 text-blue-600" />
            </div>
          </div>
        </div>

        {/* Search and Filters */}
        <div className="bg-white border border-gray-200 rounded-lg p-4 mb-6">
          <div className="flex flex-col sm:flex-row gap-4">
            <div className="flex-1">
              <div className="relative">
                <Search className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                <input
                  type="text"
                  placeholder="Cari berdasarkan nama, email, kode agent, atau kode referral..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
            </div>
            <div className="flex gap-2">
              <select
                value={statusFilter}
                onChange={(e) => setStatusFilter(e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="all">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Tidak Aktif</option>
                <option value="suspended">Disuspen</option>
              </select>
              <select
                value={levelFilter}
                onChange={(e) => setLevelFilter(e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="all">Semua Level</option>
                <option value="bronze">Bronze</option>
                <option value="silver">Silver</option>
                <option value="gold">Gold</option>
                <option value="platinum">Platinum</option>
                <option value="diamond">Diamond</option>
              </select>
              <button className="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100">
                <Filter className="w-4 h-4 mr-2" />
                Filter
              </button>
            </div>
          </div>
        </div>

        {/* Agents Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredAgents.map((agent) => (
            <AgentCard key={agent.id} agent={agent} />
          ))}
        </div>

        {filteredAgents.length === 0 && (
          <div className="text-center py-12">
            <UserCheck className="w-12 h-12 text-gray-400 mx-auto mb-4" />
            <h3 className="text-lg font-medium text-gray-900 mb-2">Tidak ada agent</h3>
            <p className="text-gray-500">
              {searchTerm || statusFilter !== 'all' || levelFilter !== 'all'
                ? 'Tidak ada agent yang sesuai dengan kriteria pencarian.'
                : 'Belum ada agent yang terdaftar. Tambahkan agent pertama Anda.'
              }
            </p>
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}
