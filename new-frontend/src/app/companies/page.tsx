'use client';

import React, { useState } from 'react';
import DashboardLayout from '@/components/DashboardLayout';
import { 
  Building2, 
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
  Globe,
  Users,
  Briefcase,
  Calendar,
  CheckCircle,
  XCircle,
  Clock
} from 'lucide-react';

interface Company {
  id: number;
  name: string;
  industry: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  province: string;
  website?: string;
  status: 'active' | 'inactive' | 'pending';
  employee_count?: string;
  established_year?: number;
  created_at: string;
  total_job_postings: number;
  active_job_postings: number;
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
  }
};

export default function CompaniesPage() {
  const [companies, setCompanies] = useState<Company[]>([
    {
      id: 1,
      name: 'PT Teknologi Maju',
      industry: 'Teknologi Informasi',
      email: 'hr@teknologimaju.com',
      phone: '+62215551234',
      address: 'Jl. Sudirman No. 123',
      city: 'Jakarta',
      province: 'DKI Jakarta',
      website: 'https://teknologimaju.com',
      status: 'active',
      employee_count: '100-500',
      established_year: 2015,
      created_at: '2025-01-15T10:00:00Z',
      total_job_postings: 8,
      active_job_postings: 5
    },
    {
      id: 2,
      name: 'CV Berkah Mandiri',
      industry: 'Perdagangan',
      email: 'info@berkahmandiri.co.id',
      phone: '+62224567890',
      address: 'Jl. Asia Afrika No. 45',
      city: 'Bandung',
      province: 'Jawa Barat',
      status: 'active',
      employee_count: '50-100',
      established_year: 2010,
      created_at: '2025-02-10T14:30:00Z',
      total_job_postings: 12,
      active_job_postings: 3
    },
    {
      id: 3,
      name: 'PT Industri Kreatif Indonesia',
      industry: 'Industri Kreatif',
      email: 'recruitment@iki.com',
      phone: '+62318765432',
      address: 'Jl. Pemuda No. 67',
      city: 'Surabaya',
      province: 'Jawa Timur',
      website: 'https://iki.com',
      status: 'pending',
      employee_count: '10-50',
      established_year: 2020,
      created_at: '2025-03-05T09:15:00Z',
      total_job_postings: 3,
      active_job_postings: 0
    },
    {
      id: 4,
      name: 'PT Solusi Digital',
      industry: 'Software Development',
      email: 'hr@solusidigital.id',
      phone: '+62274987654',
      address: 'Jl. Malioboro No. 89',
      city: 'Yogyakarta',
      province: 'DI Yogyakarta',
      website: 'https://solusidigital.id',
      status: 'active',
      employee_count: '20-50',
      established_year: 2018,
      created_at: '2025-04-12T16:20:00Z',
      total_job_postings: 6,
      active_job_postings: 4
    }
  ]);

  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [industryFilter, setIndustryFilter] = useState('all');

  const filteredCompanies = companies.filter(company => {
    const matchesSearch = searchTerm === '' || 
      company.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      company.industry.toLowerCase().includes(searchTerm.toLowerCase()) ||
      company.city.toLowerCase().includes(searchTerm.toLowerCase());
    
    const matchesStatus = statusFilter === 'all' || company.status === statusFilter;
    const matchesIndustry = industryFilter === 'all' || company.industry === industryFilter;
    
    return matchesSearch && matchesStatus && matchesIndustry;
  });

  const stats = {
    total: companies.length,
    active: companies.filter(c => c.status === 'active').length,
    pending: companies.filter(c => c.status === 'pending').length,
    inactive: companies.filter(c => c.status === 'inactive').length,
    totalJobs: companies.reduce((sum, c) => sum + c.total_job_postings, 0),
    activeJobs: companies.reduce((sum, c) => sum + c.active_job_postings, 0)
  };

  const industries = [...new Set(companies.map(c => c.industry))];

  const CompanyCard = ({ company }: { company: Company }) => {
    const StatusIcon = statusConfig[company.status].icon;
    
    return (
      <div className="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
        <div className="flex items-start justify-between mb-4">
          <div className="flex items-center space-x-3">
            <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
              <Building2 className="w-6 h-6 text-blue-600" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">{company.name}</h3>
              <p className="text-sm text-gray-500">{company.industry}</p>
            </div>
          </div>
          <div className="flex items-center space-x-2">
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusConfig[company.status].color}`}>
              <StatusIcon className="w-3 h-3 mr-1" />
              {statusConfig[company.status].label}
            </span>
            <button className="p-1 hover:bg-gray-100 rounded">
              <MoreVertical className="w-4 h-4 text-gray-400" />
            </button>
          </div>
        </div>

        <div className="space-y-2 mb-4">
          <div className="flex items-center text-sm text-gray-600">
            <Mail className="w-4 h-4 mr-2" />
            {company.email}
          </div>
          <div className="flex items-center text-sm text-gray-600">
            <Phone className="w-4 h-4 mr-2" />
            {company.phone}
          </div>
          <div className="flex items-center text-sm text-gray-600">
            <MapPin className="w-4 h-4 mr-2" />
            {company.city}, {company.province}
          </div>
          {company.website && (
            <div className="flex items-center text-sm text-gray-600">
              <Globe className="w-4 h-4 mr-2" />
              <a href={company.website} target="_blank" rel="noopener noreferrer" className="text-blue-600 hover:underline">
                Website
              </a>
            </div>
          )}
          {company.employee_count && (
            <div className="flex items-center text-sm text-gray-600">
              <Users className="w-4 h-4 mr-2" />
              {company.employee_count} karyawan
            </div>
          )}
          {company.established_year && (
            <div className="flex items-center text-sm text-gray-600">
              <Calendar className="w-4 h-4 mr-2" />
              Didirikan {company.established_year}
            </div>
          )}
        </div>

        <div className="bg-gray-50 p-3 rounded-lg mb-4">
          <div className="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span className="text-gray-600">Total Lowongan:</span>
              <span className="ml-2 font-medium">{company.total_job_postings}</span>
            </div>
            <div>
              <span className="text-gray-600">Lowongan Aktif:</span>
              <span className="ml-2 font-medium text-green-600">{company.active_job_postings}</span>
            </div>
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
            <Briefcase className="w-3 h-3 mr-1" />
            Lowongan
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
            <h1 className="text-2xl font-bold text-gray-900">Manajemen Perusahaan</h1>
            <p className="text-gray-600 mt-1">Kelola data perusahaan partner dan track lowongan kerja</p>
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
              Tambah Perusahaan
            </button>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Total Perusahaan</p>
                <p className="text-2xl font-bold text-gray-900">{stats.total}</p>
              </div>
              <Building2 className="w-8 h-8 text-blue-600" />
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
                <p className="text-sm font-medium text-gray-600">Tidak Aktif</p>
                <p className="text-2xl font-bold text-gray-600">{stats.inactive}</p>
              </div>
              <XCircle className="w-8 h-8 text-gray-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Total Lowongan</p>
                <p className="text-2xl font-bold text-purple-600">{stats.totalJobs}</p>
              </div>
              <Briefcase className="w-8 h-8 text-purple-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Lowongan Aktif</p>
                <p className="text-2xl font-bold text-blue-600">{stats.activeJobs}</p>
              </div>
              <Briefcase className="w-8 h-8 text-blue-600" />
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
                  placeholder="Cari berdasarkan nama perusahaan, industri, atau kota..."
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
                <option value="inactive">Tidak Aktif</option>
              </select>
              <select
                value={industryFilter}
                onChange={(e) => setIndustryFilter(e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="all">Semua Industri</option>
                {industries.map(industry => (
                  <option key={industry} value={industry}>{industry}</option>
                ))}
              </select>
              <button className="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100">
                <Filter className="w-4 h-4 mr-2" />
                Filter
              </button>
            </div>
          </div>
        </div>

        {/* Companies Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredCompanies.map((company) => (
            <CompanyCard key={company.id} company={company} />
          ))}
        </div>

        {filteredCompanies.length === 0 && (
          <div className="text-center py-12">
            <Building2 className="w-12 h-12 text-gray-400 mx-auto mb-4" />
            <h3 className="text-lg font-medium text-gray-900 mb-2">Tidak ada perusahaan</h3>
            <p className="text-gray-500">
              {searchTerm || statusFilter !== 'all' || industryFilter !== 'all'
                ? 'Tidak ada perusahaan yang sesuai dengan kriteria pencarian.'
                : 'Belum ada perusahaan yang terdaftar. Tambahkan perusahaan partner pertama Anda.'
              }
            </p>
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}
