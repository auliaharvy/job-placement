'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import DashboardLayout from '@/components/DashboardLayout';
import { 
  Users, 
  Search, 
  Filter, 
  Plus, 
  MoreVertical,
  Eye,
  Edit,
  Trash2,
  Download,
  Upload,
  UserCheck,
  Clock,
  AlertCircle,
  CheckCircle,
  XCircle,
  Phone,
  Mail,
  MapPin,
  GraduationCap,
  Briefcase,
  Calendar
} from 'lucide-react';

interface Applicant {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone: string;
  age: number;
  city: string;
  province: string;
  education_level: string;
  work_status: string;
  status: 'active' | 'inactive' | 'pending' | 'placed';
  created_at: string;
  total_applications: number;
  agent?: {
    id: number;
    agent_code: string;
    user: {
      full_name: string;
    };
  };
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
  pending: { 
    label: 'Pending', 
    color: 'bg-yellow-100 text-yellow-800', 
    icon: Clock 
  },
  placed: { 
    label: 'Ditempatkan', 
    color: 'bg-blue-100 text-blue-800', 
    icon: UserCheck 
  }
};

export default function ApplicantsPage() {
  const { user } = useAuth();
  
  // Mock data - replace with API call
  const [applicants, setApplicants] = useState<Applicant[]>([
    {
      id: 1,
      first_name: 'Budi',
      last_name: 'Santoso',
      email: 'budi.santoso@email.com',
      phone: '+62812345678',
      age: 28,
      city: 'Jakarta',
      province: 'DKI Jakarta',
      education_level: 'S1',
      work_status: 'unemployed',
      status: 'active',
      created_at: '2025-06-01T10:00:00Z',
      total_applications: 3,
      agent: {
        id: 1,
        agent_code: 'AGT001',
        user: { full_name: 'John Doe' }
      }
    },
    {
      id: 2,
      first_name: 'Siti',
      last_name: 'Nurhaliza',
      email: 'siti.nurhaliza@email.com',
      phone: '+62823456789',
      age: 25,
      city: 'Bandung',
      province: 'Jawa Barat',
      education_level: 'D3',
      work_status: 'employed',
      status: 'placed',
      created_at: '2025-05-28T14:30:00Z',
      total_applications: 5,
      agent: {
        id: 2,
        agent_code: 'AGT002',
        user: { full_name: 'Jane Smith' }
      }
    },
    {
      id: 3,
      first_name: 'Ahmad',
      last_name: 'Rahman',
      email: 'ahmad.rahman@email.com',
      phone: '+62834567890',
      age: 30,
      city: 'Surabaya',
      province: 'Jawa Timur',
      education_level: 'S1',
      work_status: 'unemployed',
      status: 'pending',
      created_at: '2025-06-03T09:15:00Z',
      total_applications: 1,
    },
    {
      id: 4,
      first_name: 'Maya',
      last_name: 'Dewi',
      email: 'maya.dewi@email.com',
      phone: '+62845678901',
      age: 24,
      city: 'Yogyakarta',
      province: 'DI Yogyakarta',
      education_level: 'S1',
      work_status: 'fresh_graduate',
      status: 'active',
      created_at: '2025-06-04T16:20:00Z',
      total_applications: 2,
    }
  ]);

  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [selectedApplicants, setSelectedApplicants] = useState<number[]>([]);
  const [showFilters, setShowFilters] = useState(false);

  const filteredApplicants = applicants.filter(applicant => {
    const matchesSearch = searchTerm === '' || 
      applicant.first_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      applicant.last_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      applicant.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
      applicant.city.toLowerCase().includes(searchTerm.toLowerCase());
    
    const matchesStatus = statusFilter === 'all' || applicant.status === statusFilter;
    
    return matchesSearch && matchesStatus;
  });

  const stats = {
    total: applicants.length,
    active: applicants.filter(a => a.status === 'active').length,
    pending: applicants.filter(a => a.status === 'pending').length,
    placed: applicants.filter(a => a.status === 'placed').length,
    inactive: applicants.filter(a => a.status === 'inactive').length,
  };

  const handleSelectAll = () => {
    if (selectedApplicants.length === filteredApplicants.length) {
      setSelectedApplicants([]);
    } else {
      setSelectedApplicants(filteredApplicants.map(a => a.id));
    }
  };

  const handleSelectApplicant = (id: number) => {
    if (selectedApplicants.includes(id)) {
      setSelectedApplicants(selectedApplicants.filter(aid => aid !== id));
    } else {
      setSelectedApplicants([...selectedApplicants, id]);
    }
  };

  const ApplicantCard = ({ applicant }: { applicant: Applicant }) => {
    const StatusIcon = statusConfig[applicant.status].icon;
    
    return (
      <div className="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
        <div className="flex items-start justify-between mb-4">
          <div className="flex items-center space-x-3">
            <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
              <Users className="w-6 h-6 text-blue-600" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">
                {applicant.first_name} {applicant.last_name}
              </h3>
              <p className="text-sm text-gray-500">ID: {applicant.id}</p>
            </div>
          </div>
          <div className="flex items-center space-x-2">
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusConfig[applicant.status].color}`}>
              <StatusIcon className="w-3 h-3 mr-1" />
              {statusConfig[applicant.status].label}
            </span>
            <button className="p-1 hover:bg-gray-100 rounded">
              <MoreVertical className="w-4 h-4 text-gray-400" />
            </button>
          </div>
        </div>

        <div className="space-y-2 mb-4">
          <div className="flex items-center text-sm text-gray-600">
            <Mail className="w-4 h-4 mr-2" />
            {applicant.email}
          </div>
          <div className="flex items-center text-sm text-gray-600">
            <Phone className="w-4 h-4 mr-2" />
            {applicant.phone}
          </div>
          <div className="flex items-center text-sm text-gray-600">
            <MapPin className="w-4 h-4 mr-2" />
            {applicant.city}, {applicant.province}
          </div>
          <div className="flex items-center text-sm text-gray-600">
            <GraduationCap className="w-4 h-4 mr-2" />
            {applicant.education_level} • {applicant.age} tahun
          </div>
        </div>

        {applicant.agent && (
          <div className="bg-blue-50 p-3 rounded-lg mb-4">
            <div className="flex items-center text-sm">
              <UserCheck className="w-4 h-4 text-blue-600 mr-2" />
              <span className="text-blue-800">
                Agent: {applicant.agent.user.full_name} ({applicant.agent.agent_code})
              </span>
            </div>
          </div>
        )}

        <div className="flex items-center justify-between">
          <div className="text-sm text-gray-500">
            {applicant.total_applications} aplikasi
          </div>
          <div className="flex space-x-2">
            <button className="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100">
              <Eye className="w-3 h-3 mr-1" />
              Detail
            </button>
            <button className="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-md hover:bg-gray-100">
              <Edit className="w-3 h-3 mr-1" />
              Edit
            </button>
          </div>
        </div>
      </div>
    );
  };

  return (
    <DashboardLayout>
      <div className="mb-8">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Manajemen Pelamar</h1>
            <p className="text-gray-600 mt-1">Kelola data pelamar kerja dan track status aplikasi</p>
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
              Tambah Pelamar
            </button>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Total Pelamar</p>
                <p className="text-2xl font-bold text-gray-900">{stats.total}</p>
              </div>
              <Users className="w-8 h-8 text-blue-600" />
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
                <p className="text-sm font-medium text-gray-600">Pending</p>
                <p className="text-2xl font-bold text-yellow-600">{stats.pending}</p>
              </div>
              <Clock className="w-8 h-8 text-yellow-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Ditempatkan</p>
                <p className="text-2xl font-bold text-blue-600">{stats.placed}</p>
              </div>
              <UserCheck className="w-8 h-8 text-blue-600" />
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
        </div>

        {/* Search and Filters */}
        <div className="bg-white border border-gray-200 rounded-lg p-4 mb-6">
          <div className="flex flex-col sm:flex-row gap-4">
            <div className="flex-1">
              <div className="relative">
                <Search className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                <input
                  type="text"
                  placeholder="Cari berdasarkan nama, email, atau kota..."
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
                <option value="pending">Pending</option>
                <option value="placed">Ditempatkan</option>
                <option value="inactive">Tidak Aktif</option>
              </select>
              <button
                onClick={() => setShowFilters(!showFilters)}
                className="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100"
              >
                <Filter className="w-4 h-4 mr-2" />
                Filter
              </button>
            </div>
          </div>
        </div>

        {/* Applicants Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredApplicants.map((applicant) => (
            <ApplicantCard key={applicant.id} applicant={applicant} />
          ))}
        </div>

        {filteredApplicants.length === 0 && (
          <div className="text-center py-12">
            <Users className="w-12 h-12 text-gray-400 mx-auto mb-4" />
            <h3 className="text-lg font-medium text-gray-900 mb-2">Tidak ada pelamar</h3>
            <p className="text-gray-500">
              {searchTerm || statusFilter !== 'all' 
                ? 'Tidak ada pelamar yang sesuai dengan kriteria pencarian.'
                : 'Belum ada pelamar yang terdaftar. Tambahkan pelamar pertama Anda.'
              }
            </p>
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}
