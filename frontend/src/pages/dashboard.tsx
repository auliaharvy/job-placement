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
import {
  LineChart,
  Line,
  AreaChart,
  Area,
  BarChart,
  Bar,
  PieChart,
  Pie,
  Cell,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  ResponsiveContainer,
} from 'recharts';
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
  charts: {
    applicants_trend: Array<{
      date: string;
      count: number;
      formatted_date: string;
    }>;
    applications_pipeline: Array<{
      stage: string;
      label: string;
      count: number;
    }>;
    placements_by_company: Array<{
      company_name: string;
      count: number;
    }>;
    whatsapp_delivery_stats: {
      total_sent: number;
      delivered: number;
      failed: number;
      pending: number;
      delivery_rate: number;
    };
    agent_performance: Array<{
      name: string;
      agent_code: string;
      total_referrals: number;
      successful_placements: number;
      success_rate: number;
      level: string;
    }>;
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
          charts: {
            applicants_trend: [
              { date: '2024-06-01', count: 12, formatted_date: 'Jun 1' },
              { date: '2024-06-02', count: 8, formatted_date: 'Jun 2' },
              { date: '2024-06-03', count: 15, formatted_date: 'Jun 3' },
              { date: '2024-06-04', count: 22, formatted_date: 'Jun 4' },
              { date: '2024-06-05', count: 18, formatted_date: 'Jun 5' },
            ],
            applications_pipeline: [
              { stage: 'applied', label: 'Applied', count: 156 },
              { stage: 'screening', label: 'Screening', count: 89 },
              { stage: 'psikotes', label: 'Psikotes', count: 67 },
              { stage: 'interview', label: 'Interview', count: 45 },
              { stage: 'medical', label: 'Medical', count: 23 },
              { stage: 'accepted', label: 'Accepted', count: 34 },
              { stage: 'rejected', label: 'Rejected', count: 78 },
            ],
            placements_by_company: [
              { company_name: 'PT Teknologi Maju', count: 45 },
              { company_name: 'CV Berkah Mandiri', count: 38 },
              { company_name: 'PT Retail Nusantara', count: 32 },
              { company_name: 'Hotel Grand Permata', count: 28 },
              { company_name: 'PT Logistik Express', count: 25 },
            ],
            whatsapp_delivery_stats: {
              total_sent: 2847,
              delivered: 2634,
              failed: 123,
              pending: 90,
              delivery_rate: 92.5,
            },
            agent_performance: [
              { name: 'Dedi Kurniawan', agent_code: 'AGT002', total_referrals: 32, successful_placements: 22, success_rate: 68.75, level: 'platinum' },
              { name: 'Rini Maharani', agent_code: 'AGT001', total_referrals: 25, successful_placements: 18, success_rate: 72.0, level: 'gold' },
            ],
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

  const colors = ['#8884d8', '#82ca9d', '#ffc658', '#ff7300', '#00ff00'];

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

        {/* Charts Row 1 */}
        <Row gutter={[16, 16]} style={{ marginBottom: 24 }}>
          {/* Applicants Trend */}
          <Col xs={24} lg={12}>
            <Card title="Trend Pendaftaran Pelamar" size="small">
              <ResponsiveContainer width="100%" height={300}>
                <AreaChart data={dashboardData?.charts.applicants_trend}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="formatted_date" />
                  <YAxis />
                  <Tooltip />
                  <Area type="monotone" dataKey="count" stroke="#1890ff" fill="#1890ff" fillOpacity={0.6} />
                </AreaChart>
              </ResponsiveContainer>
            </Card>
          </Col>

          {/* Applications Pipeline */}
          <Col xs={24} lg={12}>
            <Card title="Pipeline Seleksi" size="small">
              <ResponsiveContainer width="100%" height={300}>
                <BarChart data={dashboardData?.charts.applications_pipeline}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="label" angle={-45} textAnchor="end" height={80} />
                  <YAxis />
                  <Tooltip />
                  <Bar dataKey="count" fill="#52c41a" />
                </BarChart>
              </ResponsiveContainer>
            </Card>
          </Col>
        </Row>

        {/* Charts Row 2 */}
        <Row gutter={[16, 16]} style={{ marginBottom: 24 }}>
          {/* Placements by Company */}
          <Col xs={24} lg={12}>
            <Card title="Penempatan per Perusahaan" size="small">
              <ResponsiveContainer width="100%" height={300}>
                <PieChart>
                  <Pie
                    data={dashboardData?.charts.placements_by_company}
                    cx="50%"
                    cy="50%"
                    labelLine={false}
                    label={({ company_name, percent }) => `${company_name} ${(percent * 100).toFixed(0)}%`}
                    outerRadius={80}
                    fill="#8884d8"
                    dataKey="count"
                  >
                    {dashboardData?.charts.placements_by_company.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={colors[index % colors.length]} />
                    ))}
                  </Pie>
                  <Tooltip />
                </PieChart>
              </ResponsiveContainer>
            </Card>
          </Col>

          {/* WhatsApp Stats */}
          <Col xs={24} lg={12}>
            <Card title="Statistik WhatsApp" size="small">
              <Row gutter={16}>
                <Col span={12}>
                  <Statistic
                    title="Total Terkirim"
                    value={dashboardData?.charts.whatsapp_delivery_stats.total_sent}
                    prefix={<CheckCircleOutlined />}
                  />
                </Col>
                <Col span={12}>
                  <Statistic
                    title="Delivery Rate"
                    value={dashboardData?.charts.whatsapp_delivery_stats.delivery_rate}
                    suffix="%"
                    precision={1}
                    valueStyle={{ color: '#52c41a' }}
                    prefix={<ArrowUpOutlined />}
                  />
                </Col>
              </Row>
              <Row gutter={16} style={{ marginTop: 16 }}>
                <Col span={8}>
                  <Statistic
                    title="Delivered"
                    value={dashboardData?.charts.whatsapp_delivery_stats.delivered}
                    valueStyle={{ color: '#52c41a' }}
                  />
                </Col>
                <Col span={8}>
                  <Statistic
                    title="Failed"
                    value={dashboardData?.charts.whatsapp_delivery_stats.failed}
                    valueStyle={{ color: '#ff4d4f' }}
                  />
                </Col>
                <Col span={8}>
                  <Statistic
                    title="Pending"
                    value={dashboardData?.charts.whatsapp_delivery_stats.pending}
                    valueStyle={{ color: '#faad14' }}
                  />
                </Col>
              </Row>
            </Card>
          </Col>
        </Row>

        {/* Bottom Section */}
        <Row gutter={[16, 16]}>
          {/* Agent Performance */}
          <Col xs={24} lg={12}>
            <Card title="Top Agent Performance" size="small">
              <List
                itemLayout="horizontal"
                dataSource={dashboardData?.charts.agent_performance}
                renderItem={(agent) => (
                  <List.Item>
                    <List.Item.Meta
                      avatar={
                        <Badge.Ribbon text={agent.level} color={agent.level === 'platinum' ? 'purple' : 'gold'}>
                          <Avatar icon={<TrophyOutlined />} />
                        </Badge.Ribbon>
                      }
                      title={agent.name}
                      description={
                        <Space direction="vertical" size="small">
                          <Text type="secondary">{agent.agent_code}</Text>
                          <Space>
                            <Tag color="blue">{agent.total_referrals} referral</Tag>
                            <Tag color="green">{agent.successful_placements} placement</Tag>
                            <Tag color="orange">{agent.success_rate}% success rate</Tag>
                          </Space>
                        </Space>
                      }
                    />
                  </List.Item>
                )}
              />
            </Card>
          </Col>

          {/* Recent Activities */}
          <Col xs={24} lg={12}>
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