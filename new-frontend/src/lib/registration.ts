import api from './api';

export interface ApplicantRegistrationData {
  // Personal Information
  first_name: string;
  last_name: string;
  email: string;
  phone: string;
  nik: string;
  birth_date: string;
  birth_place: string;
  gender: 'male' | 'female';
  religion?: string;
  marital_status: 'single' | 'married' | 'divorced' | 'widowed';
  height?: number;
  weight?: number;
  blood_type?: string;
  
  // Address Information
  address: string;
  city: string;
  province: string;
  postal_code?: string;
  whatsapp_number: string;
  
  // Emergency Contact
  emergency_contact_name?: string;
  emergency_contact_phone?: string;
  emergency_contact_relation?: string;
  
  // Education
  education_level: 'sd' | 'smp' | 'sma' | 'smk' | 'd1' | 'd2' | 'd3' | 's1' | 's2' | 's3';
  school_name: string;
  major?: string;
  graduation_year: number;
  gpa?: number;
  
  // Work Experience
  work_experience?: Array<{
    company: string;
    position: string;
    years: number;
  }>;
  skills?: string[];
  total_work_experience_months?: number;
  
  // Job Preferences
  preferred_positions?: string[];
  preferred_locations?: string[];
  expected_salary_min?: number;
  expected_salary_max?: number;
  
  // Additional
  agent_id?: number;
  registration_source?: string;
  notes?: string;
}

export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
  errors?: any;
}

export class RegistrationService {
  static async registerApplicant(data: ApplicantRegistrationData): Promise<any> {
    try {
      console.log('Registering applicant with data:', data);
      
      const response = await api.post<ApiResponse<any>>('/auth/register/applicant', data);
      
      console.log('Registration response:', response.data);
      
      if (response.data.success) {
        return response.data.data;
      } else {
        throw new Error(response.data.message || 'Registration failed');
      }
    } catch (error: any) {
      console.error('Registration error:', error);
      if (error.response?.data?.errors) {
        throw { message: error.response.data.message, errors: error.response.data.errors };
      }
      if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      }
      throw new Error('Network error. Please check your connection.');
    }
  }
}
