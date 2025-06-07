'use client';

import React, { useState } from 'react';
import DashboardLayout from '@/components/DashboardLayout';
import { 
  BarChart3, 
  TrendingUp, 
  Download, 
  Filter, 
  Calendar,
  Users,
  Building2,
  Briefcase,
  UserCheck,
  DollarSign,
  Target,
  Award,
  Eye,
  FileText,
  PieChart,
  LineChart
} from 'lucide-react';

export default function ReportsPage() {
  const [dateRange, setDateRange] = useState('last_30_days');
  const [reportType, setReportType] = useState('overview');

  const overviewStats = {
    totalApplicants: 156,
    totalCompanies: 24,
    totalJobs: 45,
    totalPlacements: 89,
    totalAgents: 12,
    totalCommission: 125000000,
    avgSuccessRate: 72.5,
    monthlyGrowth: 15.3
  };

  const reports = [
    {
      id: 1,
      title: 'Laporan Pelamar Bulanan',
      description: 'Data lengkap pelamar yang mendaftar dalam periode tertentu',
      icon: Users,
      type: 'applicants',
      lastGenerated: '2025-06-06 10:30',
      downloads: 45
    },
    {
      id: 2,
      title: 'Laporan Kinerja Agent',
      description: 'Performa agent berdasarkan referral dan penempatan',
      icon: UserCheck,
      type: 'agents',
      lastGenerated: '2025-06-05 15:20',
      downloads: 32
    },
    {
      id: 3,
      title: 'Laporan Lowongan Kerja',
      description: 'Statistik lowongan kerja dan tingkat aplikasi',
      icon: Briefcase,
      type: 'jobs',
      lastGenerated: '2025-06-04 09:45',
      downloads: 28
    },
    {
      id: 4,
      title: 'Laporan Penempatan',
      description: 'Data penempatan kerja dan tingkat keberhasilan',
      icon: Target,
      type: 'placements',
      lastGenerated: '2025-06-03 14:15',
      downloads: 51
    },
    {
      id: 5,
      title: 'Laporan Perusahaan',
      description: 'Profil perusahaan partner dan aktivitas recruitment',
      icon: Building2,
      type: 'companies',
      lastGenerated: '2025-06-02 11:00',
      downloads: 19
    },
    {
      id: 6,
      title: 'Laporan Komisi',
      description: 'Rincian komisi agent dan pembayaran',
      icon: DollarSign,
      type: 'commission',
      lastGenerated: '2025-06-01 16:30',
      downloads: 67
    }
  ];

  const performanceData = [
    { month: 'Jan', applicants: 45, placements: 32, success_rate: 71 },
    { month: 'Feb', applicants: 52, placements: 38, success_rate: 73 },
    { month: 'Mar', applicants: 48, placements: 35, success_rate: 73 },
    { month: 'Apr', applicants: 61, placements: 44, success_rate: 72 },
    { month: 'May', applicants: 58, placements: 43, success_rate: 74 },
    { month: 'Jun', applicants: 67, placements: 49, success_rate: 73 }
  ];

  const topAgents = [
    { name: 'John Doe', referrals: 45, placements: 32, commission: 25000000 },
    { name: 'Jane Smith', referrals: 28, placements: 20, commission: 15000000 },
    { name: 'Ahmad Rahman', referrals: 12, placements: 8, commission: 5000000 },
    { name: 'Siti Nurhaliza', referrals: 78, placements: 65, commission: 45000000 }
  ];

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(amount);
  };

  return (
    <DashboardLayout>
      <div className="mb-8">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
            <p className="text-gray-600 mt-1">Analisis performa dan laporan komprehensif sistem</p>
          </div>
          <div className="flex space-x-3">
            <select
              value={dateRange}
              onChange={(e) => setDateRange(e.target.value)}
              className="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="last_7_days">7 Hari Terakhir</option>
              <option value="last_30_days">30 Hari Terakhir</option>
              <option value="last_3_months">3 Bulan Terakhir</option>
              <option value="last_6_months">6 Bulan Terakhir</option>
              <option value="last_year">1 Tahun Terakhir</option>
            </select>
            <button className="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
              <Filter className="w-4 h-4 mr-2" />
              Filter
            </button>
            <button className="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
              <Download className="w-4 h-4 mr-2" />
              Export All
            </button>
          </div>
        </div>

        {/* Overview Stats */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">Total Pelamar</p>
                <p className="text-2xl font-bold text-gray-900">{overviewStats.totalApplicants}</p>
                <p className="text-xs text-green-600 flex items-center mt-1">
                  <TrendingUp className="h-3 w-3 mr-1" />
                  +{overviewStats.monthlyGrowth}% bulan ini
                </p>
              </div>
              <div className="p-3 rounded-full bg-blue-50">
                <Users className="h-6 w-6 text-blue-600" />
              </div>
            </div>
          </div>

          <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">Total Penempatan</p>
                <p className="text-2xl font-bold text-gray-900">{overviewStats.totalPlacements}</p>
                <p className="text-xs text-green-600 flex items-center mt-1">
                  <TrendingUp className="h-3 w-3 mr-1" />
                  +12.4% bulan ini
                </p>
              </div>
              <div className="p-3 rounded-full bg-green-50">
                <Target className="h-6 w-6 text-green-600" />
              </div>
            </div>
          </div>

          <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">Success Rate</p>
                <p className="text-2xl font-bold text-gray-900">{overviewStats.avgSuccessRate}%</p>
                <p className="text-xs text-green-600 flex items-center mt-1">
                  <TrendingUp className="h-3 w-3 mr-1" />
                  +2.1% bulan ini
                </p>
              </div>
              <div className="p-3 rounded-full bg-purple-50">
                <Award className="h-6 w-6 text-purple-600" />
              </div>
            </div>
          </div>

          <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">Total Komisi</p>
                <p className="text-2xl font-bold text-gray-900">{(overviewStats.totalCommission / 1000000).toFixed(0)}M</p>
                <p className="text-xs text-green-600 flex items-center mt-1">
                  <TrendingUp className="h-3 w-3 mr-1" />
                  +8.7% bulan ini
                </p>
              </div>
              <div className="p-3 rounded-full bg-orange-50">
                <DollarSign className="h-6 w-6 text-orange-600" />
              </div>
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          {/* Performance Chart Placeholder */}
          <div className="bg-white border border-gray-200 rounded-lg p-6">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-lg font-semibold text-gray-900">Performa 6 Bulan Terakhir</h3>
              <LineChart className="w-5 h-5 text-gray-400" />
            </div>
            <div className="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
              <div className="text-center">
                <BarChart3 className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                <p className="text-gray-500">Chart akan ditampilkan di sini</p>
                <p className="text-sm text-gray-400">Performa pelamar dan penempatan</p>
              </div>
            </div>
          </div>

          {/* Top Agents */}
          <div className="bg-white border border-gray-200 rounded-lg p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">Top Performing Agents</h3>
            <div className="space-y-4">
              {topAgents.map((agent, index) => (
                <div key={index} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div className="flex items-center space-x-3">
                    <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                      <span className="text-sm font-semibold text-blue-600">{index + 1}</span>
                    </div>
                    <div>
                      <p className="font-medium text-gray-900">{agent.name}</p>
                      <p className="text-sm text-gray-500">{agent.referrals} referrals • {agent.placements} placements</p>
                    </div>
                  </div>
                  <div className="text-right">
                    <p className="font-semibold text-green-600">{formatCurrency(agent.commission)}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Available Reports */}
        <div className="bg-white border border-gray-200 rounded-lg p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-6">Laporan Tersedia</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {reports.map((report) => {
              const Icon = report.icon;
              return (
                <div key={report.id} className="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                  <div className="flex items-start justify-between mb-4">
                    <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                      <Icon className="w-5 h-5 text-blue-600" />
                    </div>
                    <span className="text-xs text-gray-500">{report.downloads} downloads</span>
                  </div>
                  
                  <h4 className="font-semibold text-gray-900 mb-2">{report.title}</h4>
                  <p className="text-sm text-gray-600 mb-4 line-clamp-2">{report.description}</p>
                  
                  <div className="text-xs text-gray-500 mb-4">
                    Terakhir dibuat: {report.lastGenerated}
                  </div>
                  
                  <div className="flex space-x-2">
                    <button className="flex-1 inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100">
                      <Eye className="w-3 h-3 mr-1" />
                      Preview
                    </button>
                    <button className="flex-1 inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-md hover:bg-gray-100">
                      <Download className="w-3 h-3 mr-1" />
                      Download
                    </button>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}
