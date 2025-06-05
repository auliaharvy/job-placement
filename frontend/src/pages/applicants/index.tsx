import React, { useState } from 'react';
import {
  Table,
  Card,
  Space,
  Button,
  Input,
  Select,
  Tag,
  Avatar,
  Drawer,
  Descriptions,
  Typography,
  Row,
  Col,
  Statistic,
  Progress,
  Divider,
  Badge,
  message,
  Popconfirm,
  Modal,
  Form,
  DatePicker,
  Upload,
} from 'antd';
import {
  UserOutlined,
  SearchOutlined,
  EyeOutlined,
  EditOutlined,
  DeleteOutlined,
  PlusOutlined,
  DownloadOutlined,
  UploadOutlined,
  WhatsAppOutlined,
  MailOutlined,
  PhoneOutlined,
  CalendarOutlined,
  FileTextOutlined,
} from '@ant-design/icons';
import type { ColumnsType } from 'antd/es/table';
import AdminLayout from '@/components/AdminLayout';
import { formatDate, formatPhoneNumber, getStatusColor, getStatusText } from '@/utils/helpers';

const { Search } = Input;
const { Option } = Select;
const { Title, Text } = Typography;

interface Applicant {
  id: string;
  registration_number: string;
  full_name: string;
  email: string;
  phone: string;
  date_of_birth: string;
  gender: 'male' | 'female';
  education_level: string;
  work_experience_years: number;
  current_status: 'available' | 'working' | 'not_available';
  registration_date: string;
  last_activity: string;
  cv_file: string | null;
  photo: string | null;
  address: {
    province: string;
    city: string;
    district: string;
  };
  skills: string[];
  applications_count: number;
  placements_count: number;
}

const ApplicantsPage: React.FC = () => {
  const [loading, setLoading] = useState(false);
  const [searchText, setSearchText] = useState('');
  const [statusFilter, setStatusFilter] = useState<string>('all');
  const [selectedApplicant, setSelectedApplicant] = useState<Applicant | null>(null);
  const [drawerVisible, setDrawerVisible] = useState(false);
  const [modalVisible, setModalVisible] = useState(false);
  const [form] = Form.useForm();

  // Mock user data
  const user = {
    full_name: 'Sari Wijaya',
    role: 'hr_staff',
    profile_picture: null,
  };

  // Mock data - replace with actual API call
  const mockApplicants: Applicant[] = [
    {
      id: '1',
      registration_number: 'APP001',
      full_name: 'John Doe',
      email: 'john.doe@email.com',
      phone: '+628123456789',
      date_of_birth: '1995-05-15',
      gender: 'male',
      education_level: 'Sarjana',
      work_experience_years: 3,
      current_status: 'available',
      registration_date: '2024-01-15',
      last_activity: '2024-06-01',
      cv_file: 'john_doe_cv.pdf',
      photo: null,
      address: {
        province: 'DKI Jakarta',
        city: 'Jakarta Selatan',
        district: 'Kebayoran Baru',
      },
      skills: ['JavaScript', 'React', 'Node.js'],
      applications_count: 5,
      placements_count: 2,
    },
    {
      id: '2',
      registration_number: 'APP002',
      full_name: 'Jane Smith',
      email: 'jane.smith@email.com',
      phone: '+628987654321',
      date_of_birth: '1992-08-22',
      gender: 'female',
      education_level: 'Diploma',
      work_experience_years: 5,
      current_status: 'working',
      registration_date: '2024-02-10',
      last_activity: '2024-05-28',
      cv_file: 'jane_smith_cv.pdf',
      photo: null,
      address: {
        province: 'Jawa Barat',
        city: 'Bandung',
        district: 'Cidadap',
      },
      skills: ['Marketing', 'Social Media', 'Content Writing'],
      applications_count: 3,
      placements_count: 1,
    },
  ];

  const [applicants, setApplicants] = useState<Applicant[]>(mockApplicants);

  const columns: ColumnsType<Applicant> = [
    {
      title: 'No. Registrasi',
      dataIndex: 'registration_number',
      key: 'registration_number',
      width: 120,
      render: (text: string) => <Text strong>{text}</Text>,
    },
    {
      title: 'Pelamar',
      key: 'applicant',
      width: 250,
      render: (_, record) => (
        <Space>
          <Avatar 
            src={record.photo} 
            icon={<UserOutlined />}
            size="large"
          />
          <div>
            <div><Text strong>{record.full_name}</Text></div>
            <div><Text type="secondary" style={{ fontSize: 12 }}>{record.email}</Text></div>
          </div>
        </Space>
      ),
    },
    {
      title: 'Kontak',
      key: 'contact',
      width: 150,
      render: (_, record) => (
        <Space direction="vertical" size="small">
          <Text>{formatPhoneNumber(record.phone)}</Text>
          <Text type="secondary">{record.address.city}</Text>
        </Space>
      ),
    },
    {
      title: 'Pendidikan',
      dataIndex: 'education_level',
      key: 'education_level',
      width: 120,
    },
    {
      title: 'Pengalaman',
      dataIndex: 'work_experience_years',
      key: 'work_experience_years',
      width: 100,
      render: (years: number) => `${years} tahun`,
    },
    {
      title: 'Status',
      dataIndex: 'current_status',
      key: 'current_status',
      width: 120,
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
      title: 'Tanggal Daftar',
      dataIndex: 'registration_date',
      key: 'registration_date',
      width: 120,
      render: (date: string) => formatDate(date),
    },
    {
      title: 'Aksi',
      key: 'actions',
      width: 150,
      fixed: 'right',
      render: (_, record) => (
        <Space>
          <Button
            type="link"
            icon={<EyeOutlined />}
            onClick={() => handleViewApplicant(record)}
            size="small"
          />
          <Button
            type="link"
            icon={<EditOutlined />}
            onClick={() => handleEditApplicant(record)}
            size="small"
          />
          <Button
            type="link"
            icon={<WhatsAppOutlined />}
            onClick={() => handleSendWhatsApp(record)}
            size="small"
          />
        </Space>
      ),
    },
  ];

  const handleViewApplicant = (applicant: Applicant) => {
    setSelectedApplicant(applicant);
    setDrawerVisible(true);
  };

  const handleEditApplicant = (applicant: Applicant) => {
    setSelectedApplicant(applicant);
    form.setFieldsValue({
      full_name: applicant.full_name,
      email: applicant.email,
      phone: applicant.phone,
      education_level: applicant.education_level,
      work_experience_years: applicant.work_experience_years,
      current_status: applicant.current_status,
    });
    setModalVisible(true);
  };

  const handleSendWhatsApp = (applicant: Applicant) => {
    message.success(`Mengirim pesan WhatsApp ke ${applicant.full_name}`);
  };

  const handleAddApplicant = () => {
    setSelectedApplicant(null);
    form.resetFields();
    setModalVisible(true);
  };

  const handleSaveApplicant = async (values: any) => {
    try {
      setLoading(true);
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      if (selectedApplicant) {
        // Update existing applicant
        setApplicants(prev => 
          prev.map(app => 
            app.id === selectedApplicant.id 
              ? { ...app, ...values }
              : app
          )
        );
        message.success('Data pelamar berhasil diperbarui');
      } else {
        // Add new applicant
        const newApplicant: Applicant = {
          id: String(Date.now()),
          registration_number: `APP${String(applicants.length + 1).padStart(3, '0')}`,
          ...values,
          registration_date: new Date().toISOString(),
          last_activity: new Date().toISOString(),
          cv_file: null,
          photo: null,
          address: {
            province: '',
            city: '',
            district: '',
          },
          skills: [],
          applications_count: 0,
          placements_count: 0,
        };
        setApplicants(prev => [...prev, newApplicant]);
        message.success('Pelamar baru berhasil ditambahkan');
      }
      
      setModalVisible(false);
    } catch (error) {
      message.error('Terjadi kesalahan');
    } finally {
      setLoading(false);
    }
  };

  const filteredApplicants = applicants.filter(applicant => {
    const matchesSearch = searchText === '' || 
      applicant.full_name.toLowerCase().includes(searchText.toLowerCase()) ||
      applicant.email.toLowerCase().includes(searchText.toLowerCase()) ||
      applicant.registration_number.toLowerCase().includes(searchText.toLowerCase());
    
    const matchesStatus = statusFilter === 'all' || applicant.current_status === statusFilter;
    
    return matchesSearch && matchesStatus;
  });

  const stats = {
    total: applicants.length,
    available: applicants.filter(a => a.current_status === 'available').length,
    working: applicants.filter(a => a.current_status === 'working').length,
    not_available: applicants.filter(a => a.current_status === 'not_available').length,
  };

  return (
    <AdminLayout user={user}>
      <div>
        {/* Header */}
        <Row justify="space-between" align="middle" style={{ marginBottom: 24 }}>
          <Col>
            <Title level={2} style={{ margin: 0 }}>Manajemen Pelamar</Title>
            <Text type="secondary">Kelola data pelamar kerja</Text>
          </Col>
          <Col>
            <Space>
              <Button icon={<DownloadOutlined />}>
                Export Data
              </Button>
              <Button type="primary" icon={<PlusOutlined />} onClick={handleAddApplicant}>
                Tambah Pelamar
              </Button>
            </Space>
          </Col>
        </Row>

        {/* Statistics */}
        <Row gutter={16} style={{ marginBottom: 24 }}>
          <Col xs={24} sm={6}>
            <Card>
              <Statistic
                title="Total Pelamar"
                value={stats.total}
                prefix={<UserOutlined />}
              />
            </Card>
          </Col>
          <Col xs={24} sm={6}>
            <Card>
              <Statistic
                title="Tersedia"
                value={stats.available}
                valueStyle={{ color: '#52c41a' }}
              />
            </Card>
          </Col>
          <Col xs={24} sm={6}>
            <Card>
              <Statistic
                title="Bekerja"
                value={stats.working}
                valueStyle={{ color: '#1890ff' }}
              />
            </Card>
          </Col>
          <Col xs={24} sm={6}>
            <Card>
              <Statistic
                title="Tidak Tersedia"
                value={stats.not_available}
                valueStyle={{ color: '#faad14' }}
              />
            </Card>
          </Col>
        </Row>

        {/* Filters */}
        <Card style={{ marginBottom: 24 }}>
          <Row gutter={16} align="middle">
            <Col xs={24} sm={12} md={8}>
              <Search
                placeholder="Cari nama, email, atau nomor registrasi..."
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
                <Option value="available">Tersedia</Option>
                <Option value="working">Bekerja</Option>
                <Option value="not_available">Tidak Tersedia</Option>
              </Select>
            </Col>
          </Row>
        </Card>

        {/* Table */}
        <Card>
          <Table
            columns={columns}
            dataSource={filteredApplicants}
            rowKey="id"
            loading={loading}
            scroll={{ x: 1200 }}
            pagination={{
              total: filteredApplicants.length,
              pageSize: 10,
              showSizeChanger: true,
              showQuickJumper: true,
              showTotal: (total, range) => 
                `${range[0]}-${range[1]} dari ${total} pelamar`,
            }}
          />
        </Card>

        {/* View Drawer */}
        <Drawer
          title="Detail Pelamar"
          placement="right"
          size="large"
          onClose={() => setDrawerVisible(false)}
          open={drawerVisible}
        >
          {selectedApplicant && (
            <div>
              <Space direction="vertical" size="large" style={{ width: '100%' }}>
                {/* Basic Info */}
                <div style={{ textAlign: 'center' }}>
                  <Avatar 
                    src={selectedApplicant.photo} 
                    icon={<UserOutlined />}
                    size={80}
                  />
                  <Title level={4} style={{ marginTop: 16, marginBottom: 0 }}>
                    {selectedApplicant.full_name}
                  </Title>
                  <Text type="secondary">{selectedApplicant.registration_number}</Text>
                  <div style={{ marginTop: 8 }}>
                    <Tag color={getStatusColor(selectedApplicant.current_status)}>
                      {getStatusText(selectedApplicant.current_status)}
                    </Tag>
                  </div>
                </div>

                <Divider />

                {/* Contact Info */}
                <Descriptions title="Informasi Kontak" column={1}>
                  <Descriptions.Item 
                    label={<Space><MailOutlined /> Email</Space>}
                  >
                    {selectedApplicant.email}
                  </Descriptions.Item>
                  <Descriptions.Item 
                    label={<Space><PhoneOutlined /> Telepon</Space>}
                  >
                    {formatPhoneNumber(selectedApplicant.phone)}
                  </Descriptions.Item>
                  <Descriptions.Item label="Alamat">
                    {`${selectedApplicant.address.district}, ${selectedApplicant.address.city}, ${selectedApplicant.address.province}`}
                  </Descriptions.Item>
                </Descriptions>

                {/* Personal Info */}
                <Descriptions title="Informasi Pribadi" column={1}>
                  <Descriptions.Item 
                    label={<Space><CalendarOutlined /> Tanggal Lahir</Space>}
                  >
                    {formatDate(selectedApplicant.date_of_birth)}
                  </Descriptions.Item>
                  <Descriptions.Item label="Jenis Kelamin">
                    {selectedApplicant.gender === 'male' ? 'Laki-laki' : 'Perempuan'}
                  </Descriptions.Item>
                  <Descriptions.Item label="Pendidikan">
                    {selectedApplicant.education_level}
                  </Descriptions.Item>
                  <Descriptions.Item label="Pengalaman Kerja">
                    {selectedApplicant.work_experience_years} tahun
                  </Descriptions.Item>
                </Descriptions>

                {/* Skills */}
                {selectedApplicant.skills.length > 0 && (
                  <>
                    <Title level={5}>Keahlian</Title>
                    <Space wrap>
                      {selectedApplicant.skills.map((skill, index) => (
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
                        value={selectedApplicant.applications_count}
                        prefix={<FileTextOutlined />}
                      />
                    </Card>
                  </Col>
                  <Col span={12}>
                    <Card>
                      <Statistic
                        title="Penempatan"
                        value={selectedApplicant.placements_count}
                        prefix={<UserOutlined />}
                      />
                    </Card>
                  </Col>
                </Row>

                {/* CV File */}
                {selectedApplicant.cv_file && (
                  <>
                    <Title level={5}>CV/Resume</Title>
                    <Card>
                      <Space>
                        <FileTextOutlined />
                        <Text>{selectedApplicant.cv_file}</Text>
                        <Button type="link" size="small">
                          Download
                        </Button>
                      </Space>
                    </Card>
                  </>
                )}

                {/* Action Buttons */}
                <Space style={{ width: '100%', justifyContent: 'center' }}>
                  <Button 
                    type="primary" 
                    icon={<EditOutlined />}
                    onClick={() => handleEditApplicant(selectedApplicant)}
                  >
                    Edit Data
                  </Button>
                  <Button 
                    icon={<WhatsAppOutlined />}
                    onClick={() => handleSendWhatsApp(selectedApplicant)}
                  >
                    WhatsApp
                  </Button>
                  <Button 
                    icon={<MailOutlined />}
                  >
                    Email
                  </Button>
                </Space>
              </Space>
            </div>
          )}
        </Drawer>

        {/* Add/Edit Modal */}
        <Modal
          title={selectedApplicant ? 'Edit Pelamar' : 'Tambah Pelamar'}
          open={modalVisible}
          onCancel={() => setModalVisible(false)}
          footer={null}
          width={600}
        >
          <Form
            form={form}
            layout="vertical"
            onFinish={handleSaveApplicant}
          >
            <Row gutter={16}>
              <Col span={12}>
                <Form.Item
                  name="full_name"
                  label="Nama Lengkap"
                  rules={[{ required: true, message: 'Nama lengkap wajib diisi' }]}
                >
                  <Input placeholder="Masukkan nama lengkap" />
                </Form.Item>
              </Col>
              <Col span={12}>
                <Form.Item
                  name="email"
                  label="Email"
                  rules={[
                    { required: true, message: 'Email wajib diisi' },
                    { type: 'email', message: 'Format email tidak valid' }
                  ]}
                >
                  <Input placeholder="Masukkan email" />
                </Form.Item>
              </Col>
            </Row>

            <Row gutter={16}>
              <Col span={12}>
                <Form.Item
                  name="phone"
                  label="Nomor Telepon"
                  rules={[{ required: true, message: 'Nomor telepon wajib diisi' }]}
                >
                  <Input placeholder="Contoh: +628123456789" />
                </Form.Item>
              </Col>
              <Col span={12}>
                <Form.Item
                  name="date_of_birth"
                  label="Tanggal Lahir"
                  rules={[{ required: true, message: 'Tanggal lahir wajib diisi' }]}
                >
                  <DatePicker 
                    style={{ width: '100%' }}
                    placeholder="Pilih tanggal lahir"
                  />
                </Form.Item>
              </Col>
            </Row>

            <Row gutter={16}>
              <Col span={12}>
                <Form.Item
                  name="gender"
                  label="Jenis Kelamin"
                  rules={[{ required: true, message: 'Jenis kelamin wajib dipilih' }]}
                >
                  <Select placeholder="Pilih jenis kelamin">
                    <Option value="male">Laki-laki</Option>
                    <Option value="female">Perempuan</Option>
                  </Select>
                </Form.Item>
              </Col>
              <Col span={12}>
                <Form.Item
                  name="education_level"
                  label="Tingkat Pendidikan"
                  rules={[{ required: true, message: 'Tingkat pendidikan wajib dipilih' }]}
                >
                  <Select placeholder="Pilih tingkat pendidikan">
                    <Option value="SD">SD</Option>
                    <Option value="SMP">SMP</Option>
                    <Option value="SMA">SMA</Option>
                    <Option value="Diploma">Diploma</Option>
                    <Option value="Sarjana">Sarjana</Option>
                    <Option value="Magister">Magister</Option>
                    <Option value="Doktor">Doktor</Option>
                  </Select>
                </Form.Item>
              </Col>
            </Row>

            <Row gutter={16}>
              <Col span={12}>
                <Form.Item
                  name="work_experience_years"
                  label="Pengalaman Kerja (Tahun)"
                  rules={[{ required: true, message: 'Pengalaman kerja wajib diisi' }]}
                >
                  <Input type="number" placeholder="0" min={0} />
                </Form.Item>
              </Col>
              <Col span={12}>
                <Form.Item
                  name="current_status"
                  label="Status Saat Ini"
                  rules={[{ required: true, message: 'Status wajib dipilih' }]}
                >
                  <Select placeholder="Pilih status">
                    <Option value="available">Tersedia</Option>
                    <Option value="working">Bekerja</Option>
                    <Option value="not_available">Tidak Tersedia</Option>
                  </Select>
                </Form.Item>
              </Col>
            </Row>

            <Form.Item>
              <Space style={{ width: '100%', justifyContent: 'flex-end' }}>
                <Button onClick={() => setModalVisible(false)}>
                  Batal
                </Button>
                <Button type="primary" htmlType="submit" loading={loading}>
                  {selectedApplicant ? 'Update' : 'Simpan'}
                </Button>
              </Space>
            </Form.Item>
          </Form>
        </Modal>
      </div>
    </AdminLayout>
  );
};

export default ApplicantsPage;
