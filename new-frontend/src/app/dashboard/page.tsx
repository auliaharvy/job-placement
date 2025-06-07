'use client';

import { useAuth } from '@/hooks/useAuth';
import DashboardLayout from '@/components/DashboardLayout';
import { 
  Users, 
  Building2, 
  FileText, 
  CheckCircle,
  TrendingUp,
  Clock,
  Activity,
  MessageCircle,
  UserPlus,
  Briefcase,
  Calendar,
  Award,
  DollarSign
} from 'lucide-react';

// Stats component
const StatsCard = ({ title, value, icon: Icon, change, color }: any) => (
  <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
    <div className="flex items-center justify-between">
      <div>
        <p className="text-sm font-medium text-gray-600">{title}</p>
        <p className="text-2xl font-bold text-gray-900">{value}</p>
        {change && (
          <p className={`text-xs ${change.positive ? 'text-green-600' : 'text-red-600'} flex items-center mt-1`}>
            <TrendingUp className="h-3 w-3 mr-1" />
            {change.value}% from last month
          </p>
        )}
      </div>
      <div className={`p-3 rounded-full ${color}`}>
        <Icon className="h-6 w-6 text-white" />
      </div>
    </div>
  </div>
);

// Activity item component
const ActivityItem = ({ icon: Icon, title, description, time, color }: any) => (
  <div className="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg">
    <div className={`p-2 rounded-full ${color}`}>
      <Icon className="h-4 w-4 text-white" />
    </div>
    <div className="flex-1 min-w-0">
      <p className="text-sm font-medium text-gray-900">{title}</p>
      <p className="text-sm text-gray-500">{description}</p>
      <p className="text-xs text-gray-400 mt-1">{time}</p>
    </div>
  </div>
);

// Role-specific dashboard content
const AdminDashboard = ({ user }: { user: any }) => (
  <div className="space-y-6">
    <div>
      <h2 className="text-2xl font-bold text-gray-900 mb-2">Admin Dashboard</h2>
      <p className="text-gray-600 mb-6">Welcome back, {user.full_name}!</p>
      
      {/* Admin Stats */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <StatsCard
          title="Total Applicants"
          value={user.admin_stats?.total_applicants || 0}
          icon={Users}
          change={{ positive: true, value: 12.5 }}
          color="bg-blue-500"
        />
        <StatsCard
          title="Active Applicants"
          value={user.admin_stats?.active_applicants || 0}
          icon={UserPlus}
          change={{ positive: true, value: 8.2 }}
          color="bg-green-500"
        />
        <StatsCard
          title="Job Postings"
          value={user.admin_stats?.total_job_postings || 0}
          icon={FileText}
          change={{ positive: false, value: 3.1 }}
          color="bg-purple-500"
        />
        <StatsCard
          title="Active Jobs"
          value={user.admin_stats?.active_job_postings || 0}
          icon={Briefcase}
          change={{ positive: true, value: 15.3 }}
          color="bg-orange-500"
        />
      </div>

      {/* Admin Activities */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Recent Activities</h3>
          <div className="space-y-2">
            <ActivityItem
              icon={UserPlus}
              title="New Applicant Registered"
              description="John Doe submitted his application"
              time="2 minutes ago"
              color="bg-blue-500"
            />
            <ActivityItem
              icon={Briefcase}
              title="Job Posted"
              description="Software Developer at PT ABC Company"
              time="1 hour ago"
              color="bg-green-500"
            />
            <ActivityItem
              icon={Calendar}
              title="Interview Scheduled"
              description="Jane Smith - Marketing Manager position"
              time="3 hours ago"
              color="bg-purple-500"
            />
          </div>
        </div>

        <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">System Status</h3>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-3 bg-green-50 rounded-lg">
              <div className="flex items-center space-x-3">
                <div className="w-3 h-3 bg-green-500 rounded-full"></div>
                <span className="text-sm font-medium text-gray-900">Database</span>
              </div>
              <span className="text-xs text-green-600 font-medium">Online</span>
            </div>
            <div className="flex items-center justify-between p-3 bg-green-50 rounded-lg">
              <div className="flex items-center space-x-3">
                <div className="w-3 h-3 bg-green-500 rounded-full"></div>
                <span className="text-sm font-medium text-gray-900">WhatsApp</span>
              </div>
              <span className="text-xs text-green-600 font-medium">Connected</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
);

const AgentDashboard = ({ user }: { user: any }) => (
  <div className="space-y-6">
    <div>
      <h2 className="text-2xl font-bold text-gray-900 mb-2">Agent Dashboard</h2>
      <p className="text-gray-600 mb-6">Welcome back, {user.full_name}! Agent Code: {user.agent?.agent_code}</p>
      
      {/* Agent Stats */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <StatsCard
          title="Total Referrals"
          value={user.agent?.total_referrals || 0}
          icon={Users}
          color="bg-blue-500"
        />
        <StatsCard
          title="Successful Placements"
          value={user.agent?.successful_placements || 0}
          icon={CheckCircle}
          color="bg-green-500"
        />
        <StatsCard
          title="Success Rate"
          value={`${user.agent?.success_rate || 0}%`}
          icon={TrendingUp}
          color="bg-purple-500"
        />
        <StatsCard
          title="Total Commission"
          value={`Rp ${Number(user.agent?.total_commission || 0).toLocaleString()}`}
          icon={DollarSign}
          color="bg-orange-500"
        />
      </div>

      {/* Agent Info */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Agent Information</h3>
          <div className="space-y-3">
            <div className="flex justify-between">
              <span className="text-gray-600">Level:</span>
              <span className="font-medium capitalize">{user.agent?.level}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-gray-600">Referral Code:</span>
              <span className="font-medium">{user.agent?.referral_code}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-gray-600">Total Points:</span>
              <span className="font-medium">{user.agent?.total_points}</span>
            </div>
            <div className="mt-4">
              <p className="text-sm text-gray-600 mb-2">QR Code URL:</p>
              <p className="text-xs text-blue-600 break-all">{user.agent?.qr_code_url}</p>
            </div>
          </div>
        </div>

        <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Performance</h3>
          <div className="space-y-4">
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Level Progress</span>
              <span className="text-blue-600 font-medium capitalize">{user.agent?.level}</span>
            </div>
            <div className="w-full bg-gray-200 rounded-full h-2">
              <div className="bg-blue-600 h-2 rounded-full" style={{ width: '75%' }}></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
);

const ApplicantDashboard = ({ user }: { user: any }) => (
  <div className="space-y-6">
    <div>
      <h2 className="text-2xl font-bold text-gray-900 mb-2">Applicant Dashboard</h2>
      <p className="text-gray-600 mb-6">Welcome back, {user.full_name}!</p>
      
      {/* Applicant Stats */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <StatsCard
          title="Total Applications"
          value={user.applicant?.total_applications || 0}
          icon={FileText}
          color="bg-blue-500"
        />
        <StatsCard
          title="Active Applications"
          value={user.applicant?.active_applications || 0}
          icon={Clock}
          color="bg-green-500"
        />
        <StatsCard
          title="Age"
          value={user.applicant?.age || 0}
          icon={Users}
          color="bg-purple-500"
        />
        <StatsCard
          title="Profile Status"
          value={user.applicant?.profile_completed ? "Complete" : "Incomplete"}
          icon={CheckCircle}
          color={user.applicant?.profile_completed ? "bg-green-500" : "bg-red-500"}
        />
      </div>

      {/* Applicant Info */}
      <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <h3 className="text-lg font-semibold text-gray-900 mb-4">Profile Information</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p className="text-sm text-gray-600">Location:</p>
            <p className="font-medium">{user.applicant?.city}, {user.applicant?.province}</p>
          </div>
          <div>
            <p className="text-sm text-gray-600">Education:</p>
            <p className="font-medium uppercase">{user.applicant?.education_level}</p>
          </div>
          <div>
            <p className="text-sm text-gray-600">Work Status:</p>
            <p className="font-medium capitalize">{user.applicant?.work_status}</p>
          </div>
          <div>
            <p className="text-sm text-gray-600">Gender:</p>
            <p className="font-medium capitalize">{user.applicant?.gender}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
);

// Default dashboard for other roles
const DefaultDashboard = ({ user }: { user: any }) => (
  <div className="space-y-6">
    <div>
      <h2 className="text-2xl font-bold text-gray-900 mb-2">Dashboard</h2>
      <p className="text-gray-600 mb-6">Welcome back, {user.full_name}!</p>
      
      <div className="bg-white p-8 rounded-xl border border-gray-200 shadow-sm text-center">
        <Activity className="h-12 w-12 text-gray-400 mx-auto mb-4" />
        <h3 className="text-lg font-semibold text-gray-900 mb-2">Dashboard Content</h3>
        <p className="text-gray-500">Role-specific dashboard will be implemented soon.</p>
      </div>
    </div>
  </div>
);

export default function DashboardPage() {
  const { user } = useAuth();

  const renderDashboardContent = () => {
    if (!user) return <DefaultDashboard user={{}} />;
    
    switch (user.role) {
      case 'super_admin':
      case 'admin':
      case 'direktur':
      case 'hr_staff':
        return <AdminDashboard user={user} />;
      case 'agent':
        return <AgentDashboard user={user} />;
      case 'applicant':
        return <ApplicantDashboard user={user} />;
      default:
        return <DefaultDashboard user={user} />;
    }
  };

  return (
    <DashboardLayout>
      {renderDashboardContent()}
    </DashboardLayout>
  );
}
