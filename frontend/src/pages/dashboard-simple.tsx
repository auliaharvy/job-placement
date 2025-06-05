import React, { useState, useEffect } from 'react';
import {
  Row,
  Col,
  Card,
  Statistic,
  Progress,
  Table,
  List,
  Avatar,
  Badge,
  Space,
  Typography,
  Button,
  Select,
  DatePicker,
  Alert,
  Spin,
  Tag,
} from 'antd';
import {
  UserOutlined,
  BriefcaseOutlined,
  FileTextOutlined,
  TeamOutlined,
  TrophyOutlined,
  ArrowUpOutlined,
  ArrowDownOutlined,
  ExclamationCircleOutlined,
  ClockCircleOutlined,
  CheckCircleOutlined,
  WarningOutlined,
} from '@ant-design/icons';
import dayjs from 'dayjs';
import AdminLayout from '@/components/AdminLayout';

const { Title, Text } = Typography;
const { RangePicker } = DatePicker;

interface DashboardData {
  overview: {
    total_applicants: number;
    active_applicants: number;
    new_applicants_this_period: number;
    total_job_postings: number;
    active_job_postings: number;
    new_jobs_this_period: number;
    total_applications: number;
    active_applications: number;
    new_applications_this_period: number;
    total_placements: number;
    active_placements: number;
    new_placements_this_period: number;
  };
  recent_activities: Array<{
    type: string;
    message: string;
    timestamp: string;
    icon: string;
    color: string;
  }>;
  alerts: Array<{
    type: 'warning' | 'error' | 'info';
    title: string;
    message: string;
    action_url?: string;
    icon: string;
  }>;
}

const Dashboard: React.FC = () => {
  const [loading, setLoading] = useState(true);
  const [dashboardData, setDashboardData] = useState<DashboardData | null>(null);
  const [dateRange, setDateRange] = useState([
    dayjs().startOf('month'),
    dayjs().endOf('month'),
  ]);

  // Mock user data - in real app, get from auth context
  const user = {
    full_name: 'Sari Wijaya',
    role: 'hr_staff',
    profile_picture: null,
  };

  useEffect(() => {
    fetchDashboardData();
  }, [dateRange]);

  const fetchDashboardData = async () => {
    setLoading(true);
    try {
      // Mock API call - replace with actual API
      setTimeout(() => {
        setDashboardData({
          overview: {
            total_applicants: 1247,
            active_applicants: 892,
            new_applicants_this_period: 45,
            total_job_postings: 156,
            active_job_postings: 23,
            new_jobs_this_period: 8,
            total_applications: 2894,
            active_applications: 167,
            new_applications_this_period: 89,
            total_placements: 634,
            active_placements: 287,
            new_placements_this_period: 34,
          },
          recent_activities: [
            { type: 'applicant_registered', message: 'John Doe mendaftar sebagai pelamar baru', timestamp: '2024-06-03T10:30:00Z', icon: 'user-plus', color: 'green' },
            { type: 'job_posted', message: 'Lowongan Full Stack Developer dipublikasikan', timestamp: '2024-06-03T09:15:00Z', icon: 'briefcase', color: 'blue' },
            { type: 'placement_created', message: 'Jane Smith ditempatkan di PT Teknologi Maju', timestamp: '2024-06-03T08:45:00Z', icon: 'check-circle', color: 'green' },
          ],
          alerts: [
            { type: 'warning', title: 'Kontrak Berakhir', message: '15 kontrak akan berakhir dalam 30 hari', icon: 'clock' },
            { type: 'error', title: 'Lowongan Urgent', message: '3 lowongan urgent membutuhkan perhatian', icon: 'exclamation-triangle' },
            { type: 'info', title: 'Review Pending', message: '23 lamaran menunggu review lebih dari 3 hari', icon: 'file-text' },
          ],
        });
        setLoading(false);
      }, 1000);
    } catch (error) {
      console.error('Failed to fetch dashboard data:', error);
      setLoading(false);
    }
  };

  const getStatisticIcon = (type: string) => {
    switch (type) {
      case 'applicants': return <UserOutlined />;
      case 'jobs': return <BriefcaseOutlined />;
      case 'applications': return <FileTextOutlined />;
      case 'placements': return <TeamOutlined />;
      default: return <UserOutlined />;
    }
  };

  const getAlertIcon = (iconName: string) => {
    switch (iconName) {
      case 'clock': return <ClockCircleOutlined />;
      case 'exclamation-triangle': return <WarningOutlined />;
      case 'file-text': return <FileTextOutlined />;
      default: return <ExclamationCircleOutlined />;
    }
  };

  if (loading) {
    return (
      <AdminLayout user={user}>
        <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '50vh' }}>
          <Spin size="large" />
        </div>
      </AdminLayout>
    );
  }

  return (
    <AdminLayout user={user}>
      <div>
        {/* Header */}
        <Row justify="space-between" align="middle" style={{ marginBottom: 24 }}>
          <Col>
            <Title level={2} style={{ margin: 0 }}>Dashboard</Title>
            <Text type="secondary">Ringkasan aktivitas dan statistik sistem</Text>
          </Col>
          <Col>
            <Space>
              <RangePicker
                value={dateRange}
                onChange={(dates) => setDateRange(dates || [])}
                format="DD MMM YYYY"
              />
              <Button type="primary" onClick={fetchDashboardData}>
                Refresh
              </Button>
            </Space>
          </Col>
        </Row>

        {/* Alerts */}
        {dashboardData?.alerts && dashboardData.alerts.length > 0 && (
          <Row style={{ marginBottom: 24 }}>
            <Col span={24}>
              <Space direction="vertical" style={{ width: '100%' }}>
                {dashboardData.alerts.map((alert, index) => (
                  <Alert
                    key={index}
                    message={alert.title}
                    description={alert.message}
                    type={alert.type}
                    icon={getAlertIcon(alert.icon)}
                    showIcon
                    action={
                      alert.action_url && (
                        <Button size="small" type="link">
                          Lihat Detail
                        </Button>
                      )
                    }
                  />
                ))}
              </Space>
            </Col>
          </Row>
        )}

        {/* Overview Statistics */}
        <Row gutter={[16, 16]} style={{ marginBottom: 24 }}>
          <Col xs={24} sm={12} lg={6}>
            <Card>
              <Statistic
                title="Total Pelamar"
                value={dashboardData?.overview.total_applicants}
                prefix={getStatisticIcon('applicants')}
                suffix={
                  <Badge 
                    count={`+${dashboardData?.overview.new_applicants_this_period}`} 
                    style={{ backgroundColor: '#52c41a' }}
                  />
                }
              />
              <Progress 
                percent={Math.round((dashboardData?.overview.active_applicants || 0) / (dashboardData?.overview.total_applicants || 1) * 100)} 
                size="small" 
                format={() => `${dashboardData?.overview.active_applicants} aktif`}
              />
            </Card>
          </Col>
          
          <Col xs={24} sm={12} lg={6}>
            <Card>
              <Statistic
                title="Lowongan Kerja"
                value={dashboardData?.overview.total_job_postings}
                prefix={getStatisticIcon('jobs')}
                suffix={
                  <Badge 
                    count={`+${dashboardData?.overview.new_jobs_this_period}`} 
                    style={{ backgroundColor: '#1890ff' }}
                  />
                }
              />
              <Progress 
                percent={Math.round((dashboardData?.overview.active_job_postings || 0) / (dashboardData?.overview.total_job_postings || 1) * 100)} 
                size="small"
                format={() => `${dashboardData?.overview.active_job_postings} aktif`}
              />
            </Card>
          </Col>
          
          <Col xs={24} sm={12} lg={6}>
            <Card>
              <Statistic
                title="Total Lamaran"
                value={dashboardData?.overview.total_applications}
                prefix={getStatisticIcon('applications')}
                suffix={
                  <Badge 
                    count={`+${dashboardData?.overview.new_applications_this_period}`} 
                    style={{ backgroundColor: '#faad14' }}
                  />
                }
              />
              <Progress 
                percent={Math.round((dashboardData?.overview.active_applications || 0) / (dashboardData?.overview.total_applications || 1) * 100)} 
                size="small"
                format={() => `${dashboardData?.overview.active_applications} aktif`}
              />
            </Card>
          </Col>
          
          <Col xs={24} sm={12} lg={6}>
            <Card>
              <Statistic
                title="Total Penempatan"
                value={dashboardData?.overview.total_placements}
                prefix={getStatisticIcon('placements')}
                suffix={
                  <Badge 
                    count={`+${dashboardData?.overview.new_placements_this_period}`} 
                    style={{ backgroundColor: '#52c41a' }}
                  />
                }
              />
              <Progress 
                percent={Math.round((dashboardData?.overview.active_placements || 0) / (dashboardData?.overview.total_placements || 1) * 100)} 
                size="small"
                format={() => `${dashboardData?.overview.active_placements} aktif`}
              />
            </Card>
          </Col>
        </Row>

        {/* Recent Activities */}
        <Row gutter={[16, 16]}>
          <Col xs={24} lg={24}>
            <Card title="Aktivitas Terbaru" size="small">
              <List
                itemLayout="horizontal"
                dataSource={dashboardData?.recent_activities}
                renderItem={(activity) => (
                  <List.Item>
                    <List.Item.Meta
                      avatar={<Avatar style={{ backgroundColor: activity.color }} icon={<UserOutlined />} />}
                      title={activity.message}
                      description={dayjs(activity.timestamp).format('DD MMM YYYY HH:mm')}
                    />
                  </List.Item>
                )}
              />
            </Card>
          </Col>
        </Row>
      </div>
    </AdminLayout>
  );
};

export default Dashboard;
