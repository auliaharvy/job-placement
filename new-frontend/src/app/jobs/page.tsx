'use client';

import React, { useState } from 'react';
import DashboardLayout from '@/components/DashboardLayout';
import { 
  Briefcase, 
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
  DollarSign,
  Clock,
  Users,
  Building2,
  Calendar,
  CheckCircle,
  XCircle,
  Pause,
  Play,
  AlertCircle,
  Globe,
  Home,
  Coffee
} from 'lucide-react';

interface Job {
  id: number;
  title: string;
  position: string;
  company: {
    id: number;
    name: string;
    city: string;
  };
  employment_type: 'pkwt' | 'pkwtt' | 'kontrak' | 'freelance' | 'magang';
  work_arrangement: 'onsite' | 'remote' | 'hybrid';
  work_location: string;
  work_city: string;
  work_province: string;
  salary_min: number;
  salary_max: number;
  status: 'draft' | 'published' | 'closed' | 'paused';
  priority: 'low' | 'medium' | 'high' | 'urgent';
  total_positions: number;
  application_count: number;
  application_deadline: string;
  created_at: string;
  required_education_levels: string[];
  min_experience_months: number;
  required_skills: string[];
}

const statusConfig = {
  draft: { 
    label: 'Draft', 
    color: 'bg-gray-100 text-gray-800', 
    icon: Edit 
  },
  published: { 
    label: 'Dipublikasi', 
    color: 'bg-green-100 text-green-800', 
    icon: CheckCircle 
  },
  closed: { 
    label: 'Ditutup', 
    color: 'bg-red-100 text-red-800', 
    icon: XCircle 
  },
  paused: { 
    label: 'Dijeda', 
    color: 'bg-yellow-100 text-yellow-800', 
    icon: Pause 
  }
};

const priorityConfig = {
  low: { label: 'Rendah', color: 'bg-gray-100 text-gray-800' },
  medium: { label: 'Sedang', color: 'bg-blue-100 text-blue-800' },
  high: { label: 'Tinggi', color: 'bg-orange-100 text-orange-800' },
  urgent: { label: 'Mendesak', color: 'bg-red-100 text-red-800' }
};

const employmentTypeConfig = {
  pkwt: 'PKWT',
  pkwtt: 'PKWTT', 
  kontrak: 'Kontrak',
  freelance: 'Freelance',
  magang: 'Magang'
};

const workArrangementConfig = {
  onsite: { label: 'Onsite', icon: Building2 },
  remote: { label: 'Remote', icon: Home },
  hybrid: { label: 'Hybrid', icon: Coffee }
};

export default function JobsPage() {
  const [jobs, setJobs] = useState<Job[]>([
    {
      id: 1,
      title: 'Full Stack Developer',
      position: 'Full Stack Developer',
      company: {
        id: 1,
        name: 'PT Teknologi Maju',
        city: 'Jakarta'
      },
      employment_type: 'pkwt',
      work_arrangement: 'hybrid',
      work_location: 'Jakarta Office',
      work_city: 'Jakarta',
      work_province: 'DKI Jakarta',
      salary_min: 8000000,
      salary_max: 15000000,
      status: 'published',
      priority: 'high',
      total_positions: 2,
      application_count: 15,
      application_deadline: '2025-07-15',
      created_at: '2025-06-01T10:00:00Z',
      required_education_levels: ['s1'],
      min_experience_months: 24,
      required_skills: ['React', 'Node.js', 'PostgreSQL', 'TypeScript']
    },
    {
      id: 2,
      title: 'Marketing Manager',
      position: 'Marketing Manager',
      company: {
        id: 2,
        name: 'CV Berkah Mandiri',
        city: 'Bandung'
      },
      employment_type: 'pkwtt',
      work_arrangement: 'onsite',
      work_location: 'Bandung Office',
      work_city: 'Bandung',
      work_province: 'Jawa Barat',
      salary_min: 12000000,
      salary_max: 18000000,
      status: 'published',
      priority: 'medium',
      total_positions: 1,
      application_count: 8,
      application_deadline: '2025-06-30',
      created_at: '2025-05-28T14:30:00Z',
      required_education_levels: ['s1', 's2'],
      min_experience_months: 36,
      required_skills: ['Digital Marketing', 'SEO', 'Social Media', 'Analytics']
    },
    {
      id: 3,
      title: 'UI/UX Designer',
      position: 'UI/UX Designer',
      company: {
        id: 3,
        name: 'PT Industri Kreatif Indonesia',
        city: 'Surabaya'
      },
      employment_type: 'kontrak',
      work_arrangement: 'remote',
      work_location: 'Remote Work',
      work_city: 'Jakarta',
      work_province: 'DKI Jakarta',
      salary_min: 6000000,
      salary_max: 10000000,
      status: 'draft',
      priority: 'low',
      total_positions: 1,
      application_count: 0,
      application_deadline: '2025-08-01',
      created_at: '2025-06-03T09:15:00Z',
      required_education_levels: ['d3', 's1'],
      min_experience_months: 12,
      required_skills: ['Figma', 'Adobe XD', 'Sketch', 'Prototyping']
    },
    {
      id: 4,
      title: 'Data Analyst',
      position: 'Data Analyst',
      company: {
        id: 4,
        name: 'PT Solusi Digital',
        city: 'Yogyakarta'
      },
      employment_type: 'pkwt',
      work_arrangement: 'hybrid',
      work_location: 'Yogyakarta Office',
      work_city: 'Yogyakarta',
      work_province: 'DI Yogyakarta',
      salary_min: 7000000,
      salary_max: 12000000,
      status: 'paused',
      priority: 'urgent',
      total_positions: 1,
      application_count: 12,
      application_deadline: '2025-06-25',
      created_at: '2025-04-12T16:20:00Z',
      required_education_levels: ['s1'],
      min_experience_months: 18,
      required_skills: ['Python', 'SQL', 'Tableau', 'Statistics']
    }
  ]);

  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [priorityFilter, setPriorityFilter] = useState('all');
  const [employmentTypeFilter, setEmploymentTypeFilter] = useState('all');

  const filteredJobs = jobs.filter(job => {
    const matchesSearch = searchTerm === '' || 
      job.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
      job.company.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      job.work_city.toLowerCase().includes(searchTerm.toLowerCase()) ||
      job.required_skills.some(skill => skill.toLowerCase().includes(searchTerm.toLowerCase()));
    
    const matchesStatus = statusFilter === 'all' || job.status === statusFilter;
    const matchesPriority = priorityFilter === 'all' || job.priority === priorityFilter;
    const matchesEmploymentType = employmentTypeFilter === 'all' || job.employment_type === employmentTypeFilter;
    
    return matchesSearch && matchesStatus && matchesPriority && matchesEmploymentType;
  });

  const stats = {
    total: jobs.length,
    published: jobs.filter(j => j.status === 'published').length,
    draft: jobs.filter(j => j.status === 'draft').length,
    paused: jobs.filter(j => j.status === 'paused').length,
    closed: jobs.filter(j => j.status === 'closed').length,
    totalApplications: jobs.reduce((sum, j) => sum + j.application_count, 0),
    totalPositions: jobs.reduce((sum, j) => sum + j.total_positions, 0)
  };

  const formatSalary = (min: number, max: number) => {
    const formatNumber = (num: number) => {
      if (num >= 1000000) {
        return `${(num / 1000000).toFixed(0)}jt`;
      }
      return `${(num / 1000).toFixed(0)}rb`;
    };
    return `Rp ${formatNumber(min)} - ${formatNumber(max)}`;
  };

  const JobCard = ({ job }: { job: Job }) => {
    const StatusIcon = statusConfig[job.status].icon;
    const WorkArrangementIcon = workArrangementConfig[job.work_arrangement].icon;
    
    return (
      <div className="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
        <div className="flex items-start justify-between mb-4">
          <div className="flex items-center space-x-3">
            <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
              <Briefcase className="w-6 h-6 text-blue-600" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">{job.title}</h3>
              <p className="text-sm text-gray-500">{job.company.name}</p>
            </div>
          </div>
          <div className="flex items-center space-x-2">
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusConfig[job.status].color}`}>
              <StatusIcon className="w-3 h-3 mr-1" />
              {statusConfig[job.status].label}
            </span>
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${priorityConfig[job.priority].color}`}>
              {priorityConfig[job.priority].label}
            </span>
            <button className="p-1 hover:bg-gray-100 rounded">
              <MoreVertical className="w-4 h-4 text-gray-400" />
            </button>
          </div>
        </div>

        <div className="space-y-2 mb-4">
          <div className="flex items-center text-sm text-gray-600">
            <MapPin className="w-4 h-4 mr-2" />
            {job.work_city}, {job.work_province}
          </div>
          <div className="flex items-center text-sm text-gray-600">
            <WorkArrangementIcon className="w-4 h-4 mr-2" />
            {workArrangementConfig[job.work_arrangement].label} • {employmentTypeConfig[job.employment_type]}
          </div>
          <div className="flex items-center text-sm text-gray-600">
            <DollarSign className="w-4 h-4 mr-2" />
            {formatSalary(job.salary_min, job.salary_max)}
          </div>
          <div className="flex items-center text-sm text-gray-600">
            <Calendar className="w-4 h-4 mr-2" />
            Deadline: {new Date(job.application_deadline).toLocaleDateString('id-ID')}
          </div>
          <div className="flex items-center text-sm text-gray-600">
            <Users className="w-4 h-4 mr-2" />
            {job.total_positions} posisi • {job.application_count} pelamar
          </div>
        </div>

        <div className="mb-4">
          <p className="text-sm text-gray-600 mb-2">Skills yang dibutuhkan:</p>
          <div className="flex flex-wrap gap-1">
            {job.required_skills.slice(0, 3).map((skill, index) => (
              <span key={index} className="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700">
                {skill}
              </span>
            ))}
            {job.required_skills.length > 3 && (
              <span className="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-50 text-gray-700">
                +{job.required_skills.length - 3} lainnya
              </span>
            )}
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
            <Users className="w-3 h-3 mr-1" />
            Pelamar
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
            <h1 className="text-2xl font-bold text-gray-900">Manajemen Lowongan Kerja</h1>
            <p className="text-gray-600 mt-1">Kelola lowongan kerja dan track aplikasi pelamar</p>
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
              Buat Lowongan
            </button>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-7 gap-4 mb-6">
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Total Lowongan</p>
                <p className="text-2xl font-bold text-gray-900">{stats.total}</p>
              </div>
              <Briefcase className="w-8 h-8 text-blue-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Dipublikasi</p>
                <p className="text-2xl font-bold text-green-600">{stats.published}</p>
              </div>
              <CheckCircle className="w-8 h-8 text-green-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Draft</p>
                <p className="text-2xl font-bold text-gray-600">{stats.draft}</p>
              </div>
              <Edit className="w-8 h-8 text-gray-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Dijeda</p>
                <p className="text-2xl font-bold text-yellow-600">{stats.paused}</p>
              </div>
              <Pause className="w-8 h-8 text-yellow-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Ditutup</p>
                <p className="text-2xl font-bold text-red-600">{stats.closed}</p>
              </div>
              <XCircle className="w-8 h-8 text-red-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Total Posisi</p>
                <p className="text-2xl font-bold text-purple-600">{stats.totalPositions}</p>
              </div>
              <Users className="w-8 h-8 text-purple-600" />
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg border border-gray-200">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Total Aplikasi</p>
                <p className="text-2xl font-bold text-orange-600">{stats.totalApplications}</p>
              </div>
              <Users className="w-8 h-8 text-orange-600" />
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
                  placeholder="Cari berdasarkan judul, perusahaan, kota, atau skill..."
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
                <option value="draft">Draft</option>
                <option value="published">Dipublikasi</option>
                <option value="paused">Dijeda</option>
                <option value="closed">Ditutup</option>
              </select>
              <select
                value={priorityFilter}
                onChange={(e) => setPriorityFilter(e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="all">Semua Prioritas</option>
                <option value="low">Rendah</option>
                <option value="medium">Sedang</option>
                <option value="high">Tinggi</option>
                <option value="urgent">Mendesak</option>
              </select>
              <select
                value={employmentTypeFilter}
                onChange={(e) => setEmploymentTypeFilter(e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="all">Semua Tipe</option>
                <option value="pkwt">PKWT</option>
                <option value="pkwtt">PKWTT</option>
                <option value="kontrak">Kontrak</option>
                <option value="freelance">Freelance</option>
                <option value="magang">Magang</option>
              </select>
              <button className="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100">
                <Filter className="w-4 h-4 mr-2" />
                Filter
              </button>
            </div>
          </div>
        </div>

        {/* Jobs Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredJobs.map((job) => (
            <JobCard key={job.id} job={job} />
          ))}
        </div>

        {filteredJobs.length === 0 && (
          <div className="text-center py-12">
            <Briefcase className="w-12 h-12 text-gray-400 mx-auto mb-4" />
            <h3 className="text-lg font-medium text-gray-900 mb-2">Tidak ada lowongan</h3>
            <p className="text-gray-500">
              {searchTerm || statusFilter !== 'all' || priorityFilter !== 'all' || employmentTypeFilter !== 'all'
                ? 'Tidak ada lowongan yang sesuai dengan kriteria pencarian.'
                : 'Belum ada lowongan yang dibuat. Buat lowongan pertama Anda.'
              }
            </p>
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}
