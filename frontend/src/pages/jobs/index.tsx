import React, { useState } from 'react';
import {
  Table,
  Card,
  Space,
  Button,
  Input,
  Select,
  Tag,
  Typography,
  Row,
  Col,
  Statistic,
  Badge,
  message,
  Modal,
  Form,
  DatePicker,
  InputNumber,
  Drawer,
  Descriptions,
  Divider,
} from 'antd';
import {
  BriefcaseOutlined,
  SearchOutlined,
  EyeOutlined,
  EditOutlined,
  DeleteOutlined,
  PlusOutlined,
  DownloadOutlined,
  CalendarOutlined,
  DollarOutlined,
  EnvironmentOutlined,
  TeamOutlined,
  ClockCircleOutlined,
} from '@ant-design/icons';
import type { ColumnsType } from 'antd/es/table';
import AdminLayout from '@/components/AdminLayout';
import { formatDate, formatCurrency, getStatusColor, getStatusText } from '@/utils/helpers';
import dayjs from 'dayjs';

const { Search, TextArea } = Input;
const { Option } = Select;
const { Title, Text } = Typography;
const { RangePicker } = DatePicker;

interface Job {
  id: string;
  job_code: string;
  title: string;
  company_name: string;
  location: string;
  job_type: 'full_time' | 'part_time' | 'contract' | 'internship';
  experience_level: 'entry' | 'mid' | 'senior' | 'executive';
  salary_min: number;
  salary_max: number;
  description: string;
  requirements: string;
  status: 'active' | 'inactive' | 'draft' | 'closed';
  posted_date: string;
  closing_date: string;
  applications_count: number;
  views_count: number;
  skills_required: string[];
  created_by: string;
}

const JobsPage: React.FC = () => {
  const [loading, setLoading] = useState(false);
  const [searchText, setSearchText] = useState('');
  const [statusFilter, setStatusFilter] = useState<string>('all');
  const [selectedJob, setSelectedJob] = useState<Job | null>(null);
  const [drawerVisible, setDrawerVisible] = useState(false);
  const [modalVisible, setModalVisible] = useState(false);
  const [form] = Form.useForm();

  // Mock user data
  const user = {
    full_name: 'Sari Wijaya',
    role: 'hr_staff',
    profile_picture: null,
  };

  // Mock data
  const mockJobs: Job[] = [
    {
      id: '1',
      job_code: 'JOB001',
      title: 'Full Stack Developer',
      company_name: 'PT Teknologi Maju',
      location: 'Jakarta Selatan',
      job_type: 'full_time',
      experience_level: 'mid',
      salary_min: 8000000,
      salary_max: 15000000,
      description: 'Mencari Full Stack Developer yang berpengalaman dalam pengembangan web aplikasi.',
      requirements: 'Minimal 2 tahun pengalaman, menguasai React, Node.js, dan database.',
      status: 'active',
      posted_date: '2024-05-15',
      closing_date: '2024-06-15',
      applications_count: 25,
      views_count: 150,
      skills_required: ['JavaScript', 'React', 'Node.js', 'MongoDB'],
      created_by: 'Sari Wijaya',
    },
    {
      id: '2',
      job_code: 'JOB002',
      title: 'Marketing Manager',
      company_name: 'CV Berkah Mandiri',
      location: 'Bandung',
      job_type: 'full_time',
      experience_level: 'senior',
      salary_min: 12000000,
      salary_max: 20000000,
      description: 'Membutuhkan Marketing Manager untuk mengembangkan strategi pemasaran.',
      requirements: 'Minimal 5 tahun pengalaman di bidang marketing, leadership skills.',
      status: 'active',
      posted_date: '2024-05-20',
      closing_date: '2024-06-20',
      applications_count: 18,
      views_count: 89,
      skills_required: ['Marketing Strategy', 'Leadership', 'Digital Marketing'],
      created_by: 'Sari Wijaya',
    },
  ];

  const [jobs, setJobs] = useState<Job[]>(mockJobs);

  const columns: ColumnsType<Job> = [
    {
      title: 'Kode',
      dataIndex: 'job_code',
      key: 'job_code',
      width: 100,
      render: (text: string) => <Text strong>{text}</Text>,
    },
    {
      title: 'Posisi',
      key: 'position',
      width: 250,
      render: (_, record) => (
        <div>
          <div><Text strong>{record.title}</Text></div>
          <div><Text type="secondary" style={{ fontSize: 12 }}>{record.company_name}</Text></div>
        </div>
      ),
    },
    {
      title: 'Lokasi',
      dataIndex: 'location',
      key: 'location',
      width: 120,
      render: (text: string) => (
        <Space>
          <EnvironmentOutlined />
          <Text>{text}</Text>
        </Space>
      ),
    },
    {
      title: 'Tipe',
      dataIndex: 'job_type',
      key: 'job_type',
      width: 120,
      render: (type: string) => {
        const typeMap = {
          full_time: { text: 'Full Time', color: 'blue' },
          part_time: { text: 'Part Time', color: 'orange' },
          contract: { text: 'Kontrak', color: 'purple' },
          internship: { text: 'Magang', color: 'green' },
        };
        const typeInfo = typeMap[type as keyof typeof typeMap];
        return <Tag color={typeInfo.color}>{typeInfo.text}</Tag>;
      },
    },
    {
      title: 'Level',
      dataIndex: 'experience_level',
      key: 'experience_level',
      width: 100,
      render: (level: string) => {
        const levelMap = {
          entry: 'Entry',
          mid: 'Mid',
          senior: 'Senior',
          executive: 'Executive',
        };
        return levelMap[level as keyof typeof levelMap];
      },
    },
    {
      title: 'Gaji',
      key: 'salary',
      width: 150,
      render: (_, record) => (
        <div>
          <Text>{formatCurrency(record.salary_min)}</Text>
          <br />
          <Text type="secondary" style={{ fontSize: 12 }}>
            - {formatCurrency(record.salary_max)}
          </Text>
        </div>
      ),
    },
    {
      title: 'Status',
      dataIndex: 'status',
      key: 'status',
      width: 100,
      render: (status: string) => (
        <Tag color={getStatusColor(status)}>
          {getStatusText(status)}
        </Tag>
      ),
    },
    {
      title: 'Lamaran',
      dataIndex: 'applications_count',
      key: 'applications_count',
      width: 80,
      align: 'center',
      render: (count: number) => (
        <Badge count={count} style={{ backgroundColor: '#1890ff' }} />
      ),
    },
    {
      title: 'Deadline',
      dataIndex: 'closing_date',
      key: 'closing_date',
      width: 120,
      render: (date: string) => {
        const isExpired = dayjs(date).isBefore(dayjs());
        return (
          <Text type={isExpired ? 'danger' : 'default'}>
            {formatDate(date)}
          </Text>
        );
      },
    },
    {
      title: 'Aksi',
      key: 'actions',
      width: 120,
      fixed: 'right',
      render: (_, record) => (
        <Space>
          <Button
            type="link"
            icon={<EyeOutlined />}
            onClick={() => handleViewJob(record)}
            size="small"
          />
          <Button
            type="link"
            icon={<EditOutlined />}
            onClick={() => handleEditJob(record)}
            size="small"
          />
          <Button
            type="link"
            icon={<DeleteOutlined />}
            onClick={() => handleDeleteJob(record.id)}
            size="small"
            danger
          />
        </Space>
      ),
    },
  ];

  const handleViewJob = (job: Job) => {
    setSelectedJob(job);
    setDrawerVisible(true);
  };

  const handleEditJob = (job: Job) => {
    setSelectedJob(job);
    form.setFieldsValue({
      ...job,
      posted_date: dayjs(job.posted_date),
      closing_date: dayjs(job.closing_date),
    });
    setModalVisible(true);
  };

  const handleDeleteJob = (jobId: string) => {
    Modal.confirm({
      title: 'Hapus Lowongan',
      content: 'Apakah Anda yakin ingin menghapus lowongan ini?',
      okText: 'Hapus',
      okType: 'danger',
      cancelText: 'Batal',
      onOk: () => {
        setJobs(prev => prev.filter(job => job.id !== jobId));
        message.success('Lowongan berhasil dihapus');
      },
    });
  };

  const handleAddJob = () => {
    setSelectedJob(null);
    form.resetFields();
    setModalVisible(true);
  };

  const handleSaveJob = async (values: any) => {
    try {
      setLoading(true);
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      const jobData = {
        ...values,
        posted_date: values.posted_date?.format('YYYY-MM-DD'),
        closing_date: values.closing_date?.format('YYYY-MM-DD'),
      };

      if (selectedJob) {
        // Update existing job
        setJobs(prev => 
          prev.map(job => 
            job.id === selectedJob.id 
              ? { ...job, ...jobData }
              : job
          )
        );
        message.success('Lowongan berhasil diperbarui');
      } else {
        // Add new job
        const newJob: Job = {
          id: String(Date.now()),
          job_code: `JOB${String(jobs.length + 1).padStart(3, '0')}`,
          ...jobData,
          applications_count: 0,
          views_count: 0,
          skills_required: [],
          created_by: user.full_name,
        };
        setJobs(prev => [...prev, newJob]);
        message.success('Lowongan baru berhasil ditambahkan');
      }
      
      setModalVisible(false);
    } catch (error) {
      message.error('Terjadi kesalahan');
    } finally {
      setLoading(false);
    }
  };

  const filteredJobs = jobs.filter(job => {
    const matchesSearch = searchText === '' || 
      job.title.toLowerCase().includes(searchText.toLowerCase()) ||
      job.company_name.toLowerCase().includes(searchText.toLowerCase()) ||
      job.job_code.toLowerCase().includes(searchText.toLowerCase());
    
    const matchesStatus = statusFilter === 'all' || job.status === statusFilter;
    
    return matchesSearch && matchesStatus;
  });

  const stats = {
    total: jobs.length,
    active: jobs.filter(j => j.status === 'active').length,
    draft: jobs.filter(j => j.status === 'draft').length,
    closed: jobs.filter(j => j.status === 'closed').length,
  };

  return (
    <AdminLayout user={user}>
      <div>
        {/* Header */}
        <Row justify="space-between" align="middle" style={{ marginBottom: 24 }}>
          <Col>
            <Title level={2} style={{ margin: 0 }}>Manajemen Lowongan</Title>
            <Text type="secondary">Kelola lowongan pekerjaan</Text>
          </Col>
          <Col>
            <Space>
              <Button icon={<DownloadOutlined />}>
                Export Data
              </Button>
              <Button type="primary" icon={<PlusOutlined />} onClick={handleAddJob}>
                Tambah Lowongan
              </Button>
            </Space>
          </Col>
        </Row>

        {/* Statistics */}
        <Row gutter={16} style={{ marginBottom: 24 }}>
          <Col xs={24} sm={6}>
            <Card>
              <Statistic
                title="Total Lowongan"
                value={stats.total}
                prefix={<BriefcaseOutlined />}
              />
            </Card>
          </Col>
          <Col xs={24} sm={6}>
            <Card>
              <Statistic
                title="Aktif"
                value={stats.active}
                valueStyle={{ color: '#52c41a' }}
              />
            </Card>
          </Col>
          <Col xs={24} sm={6}>
            <Card>
              <Statistic
                title="Draft"
                value={stats.draft}
                valueStyle={{ color: '#faad14' }}
              />
            </Card>
          </Col>
          <Col xs={24} sm={6}>
            <Card>
              <Statistic
                title="Ditutup"
                value={stats.closed}
                valueStyle={{ color: '#8c8c8c' }}
              />
            </Card>
          </Col>
        </Row>

        {/* Filters */}
        <Card style={{ marginBottom: 24 }}>
          <Row gutter={16} align="middle">
            <Col xs={24} sm={12} md={8}>
              <Search
                placeholder="Cari posisi, perusahaan, atau kode..."
                value={searchText}
                onChange={(e) => setSearchText(e.target.value)}
                style={{ width: '100%' }}
              />
            </Col>
            <Col xs={24} sm={12} md={6}>
              <Select
                placeholder="Filter Status"
                value={statusFilter}
                onChange={setStatusFilter}
                style={{ width: '100%' }}
              >
                <Option value="all">Semua Status</Option>
                <Option value="active">Aktif</Option>
                <Option value="draft">Draft</Option>
                <Option value="closed">Ditutup</Option>
                <Option value="inactive">Tidak Aktif</Option>
              </Select>
            </Col>
          </Row>
        </Card>

        {/* Table */}
        <Card>
          <Table
            columns={columns}
            dataSource={filteredJobs}
            rowKey="id"
            loading={loading}
            scroll={{ x: 1400 }}
            pagination={{
              total: filteredJobs.length,
              pageSize: 10,
              showSizeChanger: true,
              showQuickJumper: true,
              showTotal: (total, range) => 
                `${range[0]}-${range[1]} dari ${total} lowongan`,
            }}
          />
        </Card>

        {/* View Drawer */}
        <Drawer
          title="Detail Lowongan"
          placement="right"
          size="large"
          onClose={() => setDrawerVisible(false)}
          open={drawerVisible}
        >
          {selectedJob && (
            <div>
              <Space direction="vertical" size="large" style={{ width: '100%' }}>
                {/* Header */}
                <div>
                  <Title level={3}>{selectedJob.title}</Title>
                  <Space>
                    <Text strong>{selectedJob.company_name}</Text>
                    <Divider type="vertical" />
                    <Text type="secondary">{selectedJob.job_code}</Text>
                  </Space>
                  <div style={{ marginTop: 8 }}>
                    <Tag color={getStatusColor(selectedJob.status)}>
                      {getStatusText(selectedJob.status)}
                    </Tag>
                  </div>
                </div>

                <Divider />

                {/* Basic Info */}
                <Descriptions title="Informasi Dasar" column={1}>
                  <Descriptions.Item 
                    label={<Space><EnvironmentOutlined /> Lokasi</Space>}
                  >
                    {selectedJob.location}
                  </Descriptions.Item>
                  <Descriptions.Item label="Tipe Pekerjaan">
                    {selectedJob.job_type === 'full_time' ? 'Full Time' :
                     selectedJob.job_type === 'part_time' ? 'Part Time' :
                     selectedJob.job_type === 'contract' ? 'Kontrak' : 'Magang'}
                  </Descriptions.Item>
                  <Descriptions.Item label="Level Pengalaman">
                    {selectedJob.experience_level === 'entry' ? 'Entry Level' :
                     selectedJob.experience_level === 'mid' ? 'Mid Level' :
                     selectedJob.experience_level === 'senior' ? 'Senior Level' : 'Executive'}
                  </Descriptions.Item>
                  <Descriptions.Item 
                    label={<Space><DollarOutlined /> Gaji</Space>}
                  >
                    {formatCurrency(selectedJob.salary_min)} - {formatCurrency(selectedJob.salary_max)}
                  </Descriptions.Item>
                </Descriptions>

                {/* Dates */}
                <Descriptions title="Timeline" column={1}>
                  <Descriptions.Item 
                    label={<Space><CalendarOutlined /> Tanggal Posting</Space>}
                  >
                    {formatDate(selectedJob.posted_date)}
                  </Descriptions.Item>
                  <Descriptions.Item 
                    label={<Space><ClockCircleOutlined /> Deadline</Space>}
                  >
                    {formatDate(selectedJob.closing_date)}
                  </Descriptions.Item>
                  <Descriptions.Item label="Dibuat oleh">
                    {selectedJob.created_by}
                  </Descriptions.Item>
                </Descriptions>

                {/* Description */}
                <div>
                  <Title level={5}>Deskripsi Pekerjaan</Title>
                  <Text>{selectedJob.description}</Text>
                </div>

                {/* Requirements */}
                <div>
                  <Title level={5}>Persyaratan</Title>
                  <Text>{selectedJob.requirements}</Text>
                </div>

                {/* Skills */}
                {selectedJob.skills_required.length > 0 && (
                  <>
                    <Title level={5}>Keahlian yang Dibutuhkan</Title>
                    <Space wrap>
                      {selectedJob.skills_required.map((skill, index) => (
                        <Tag key={index} color="blue">{skill}</Tag>
                      ))}
                    </Space>
                  </>
                )}

                {/* Statistics */}
                <Row gutter={16}>
                  <Col span={12}>
                    <Card>
                      <Statistic
                        title="Total Lamaran"
                        value={selectedJob.applications_count}
                        prefix={<TeamOutlined />}
                      />
                    </Card>
                  </Col>
                  <Col span={12}>
                    <Card>
                      <Statistic
                        title="Views"
                        value={selectedJob.views_count}
                        prefix={<EyeOutlined />}
                      />
                    </Card>
                  </Col>
                </Row>

                {/* Action Buttons */}
                <Space style={{ width: '100%', justifyContent: 'center' }}>
                  <Button 
                    type="primary" 
                    icon={<EditOutlined />}
                    onClick={() => handleEditJob(selectedJob)}
                  >
                    Edit Lowongan
                  </Button>
                  <Button 
                    icon={<TeamOutlined />}
                  >
                    Lihat Lamaran
                  </Button>
                </Space>
              </Space>
            </div>
          )}
        </Drawer>

        {/* Add/Edit Modal */}
        <Modal
          title={selectedJob ? 'Edit Lowongan' : 'Tambah Lowongan'}
          open={modalVisible}
          onCancel={() => setModalVisible(false)}
          footer={null}
          width={800}
        >
          <Form
            form={form}
            layout="vertical"
            onFinish={handleSaveJob}
          >
            <Row gutter={16}>
              <Col span={12}>
                <Form.Item
                  name="title"
                  label="Judul Posisi"
                  rules={[{ required: true, message: 'Judul posisi wajib diisi' }]}
                >
                  <Input placeholder="Contoh: Full Stack Developer" />
                </Form.Item>
              </Col>
              <Col span={12}>
                <Form.Item
                  name="company_name"
                  label="Nama Perusahaan"
                  rules={[{ required: true, message: 'Nama perusahaan wajib diisi' }]}
                >
                  <Input placeholder="Contoh: PT Teknologi Maju" />
                </Form.Item>
              </Col>
            </Row>

            <Row gutter={16}>
              <Col span={12}>
                <Form.Item
                  name="location"
                  label="Lokasi"
                  rules={[{ required: true, message: 'Lokasi wajib diisi' }]}
                >
                  <Input placeholder="Contoh: Jakarta Selatan" />
                </Form.Item>
              </Col>
              <Col span={12}>
                <Form.Item
                  name="job_type"
                  label="Tipe Pekerjaan"
                  rules={[{ required: true, message: 'Tipe pekerjaan wajib dipilih' }]}
                >
                  <Select placeholder="Pilih tipe pekerjaan">
                    <Option value="full_time">Full Time</Option>
                    <Option value="part_time">Part Time</Option>
                    <Option value="contract">Kontrak</Option>
                    <Option value="internship">Magang</Option>
                  </Select>
                </Form.Item>
              </Col>
            </Row>

            <Row gutter={16}>
              <Col span={12}>
                <Form.Item
                  name="experience_level"
                  label="Level Pengalaman"
                  rules={[{ required: true, message: 'Level pengalaman wajib dipilih' }]}
                >
                  <Select placeholder="Pilih level pengalaman">
                    <Option value="entry">Entry Level</Option>
                    <Option value="mid">Mid Level</Option>
                    <Option value="senior">Senior Level</Option>
                    <Option value="executive">Executive</Option>
                  </Select>
                </Form.Item>
              </Col>
              <Col span={12}>
                <Form.Item
                  name="status"
                  label="Status"
                  rules={[{ required: true, message: 'Status wajib dipilih' }]}
                >
                  <Select placeholder="Pilih status">
                    <Option value="draft">Draft</Option>
                    <Option value="active">Aktif</Option>
                    <Option value="inactive">Tidak Aktif</Option>
                    <Option value="closed">Ditutup</Option>
                  </Select>
                </Form.Item>
              </Col>
            </Row>

            <Row gutter={16}>
              <Col span={12}>
                <Form.Item
                  name="salary_min"
                  label="Gaji Minimum"
                  rules={[{ required: true, message: 'Gaji minimum wajib diisi' }]}
                >
                  <InputNumber
                    style={{ width: '100%' }}
                    placeholder="5000000"
                    formatter={value => `Rp ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')}
                    parser={value => value!.replace(/Rp\s?|(,*)/g, '') as any}
                  />
                </Form.Item>
              </Col>
              <Col span={12}>
                <Form.Item
                  name="salary_max"
                  label="Gaji Maksimum"
                  rules={[{ required: true, message: 'Gaji maksimum wajib diisi' }]}
                >
                  <InputNumber
                    style={{ width: '100%' }}
                    placeholder="10000000"
                    formatter={value => `Rp ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')}
                    parser={value => value!.replace(/Rp\s?|(,*)/g, '') as any}
                  />
                </Form.Item>
              </Col>
            </Row>

            <Row gutter={16}>
              <Col span={12}>
                <Form.Item
                  name="posted_date"
                  label="Tanggal Posting"
                  rules={[{ required: true, message: 'Tanggal posting wajib diisi' }]}
                >
                  <DatePicker 
                    style={{ width: '100%' }}
                    placeholder="Pilih tanggal posting"
                  />
                </Form.Item>
              </Col>
              <Col span={12}>
                <Form.Item
                  name="closing_date"
                  label="Tanggal Penutupan"
                  rules={[{ required: true, message: 'Tanggal penutupan wajib diisi' }]}
                >
                  <DatePicker 
                    style={{ width: '100%' }}
                    placeholder="Pilih tanggal penutupan"
                  />
                </Form.Item>
              </Col>
            </Row>

            <Form.Item
              name="description"
              label="Deskripsi Pekerjaan"
              rules={[{ required: true, message: 'Deskripsi pekerjaan wajib diisi' }]}
            >
              <TextArea 
                rows={4}
                placeholder="Jelaskan detail pekerjaan, tanggung jawab, dan benefit..."
              />
            </Form.Item>

            <Form.Item
              name="requirements"
              label="Persyaratan"
              rules={[{ required: true, message: 'Persyaratan wajib diisi' }]}
            >
              <TextArea 
                rows={3}
                placeholder="Jelaskan kualifikasi dan persyaratan yang dibutuhkan..."
              />
            </Form.Item>

            <Form.Item>
              <Space style={{ width: '100%', justifyContent: 'flex-end' }}>
                <Button onClick={() => setModalVisible(false)}>
                  Batal
                </Button>
                <Button type="primary" htmlType="submit" loading={loading}>
                  {selectedJob ? 'Update' : 'Simpan'}
                </Button>
              </Space>
            </Form.Item>
          </Form>
        </Modal>
      </div>
    </AdminLayout>
  );
};

export default JobsPage;
