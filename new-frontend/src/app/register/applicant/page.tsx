'use client';

import { useState, useEffect } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import { useForm } from 'react-hook-form';
import { 
  User, 
  MapPin, 
  GraduationCap, 
  Briefcase, 
  Target,
  ChevronLeft,
  ChevronRight,
  Check,
  UserPlus,
  AlertCircle
} from 'lucide-react';
import { RegistrationService, ApplicantRegistrationData } from '@/lib/registration';
import { AgentService } from '@/lib/agent';
import PersonalInfoStep from '@/components/registration/PersonalInfoStep';
import AddressContactStep from '@/components/registration/AddressContactStep';
import EducationStep from '@/components/registration/EducationStep';
import ExperienceSkillsStep from '@/components/registration/ExperienceSkillsStep';
import JobPreferencesStep from '@/components/registration/JobPreferencesStep';

interface FormData extends ApplicantRegistrationData {}

const STEPS = [
  { id: 1, name: 'Data Pribadi', icon: User },
  { id: 2, name: 'Alamat & Kontak', icon: MapPin },
  { id: 3, name: 'Pendidikan', icon: GraduationCap },
  { id: 4, name: 'Pengalaman Kerja', icon: Briefcase },
  { id: 5, name: 'Preferensi Kerja', icon: Target },
];

export default function ApplicantRegisterPage() {
  const [currentStep, setCurrentStep] = useState(1);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitError, setSubmitError] = useState('');
  const [selectedAgentId, setSelectedAgentId] = useState<number | undefined>();
  const [agentNotification, setAgentNotification] = useState('');
  
  const router = useRouter();
  const searchParams = useSearchParams();

  const { 
    register, 
    handleSubmit, 
    control,
    watch,
    trigger,
    formState: { errors },
    setValue
  } = useForm<FormData>({
    defaultValues: {
      gender: 'male',
      marital_status: 'single',
      education_level: 's1',
      work_experience: [],
      skills: [],
      preferred_positions: [],
      preferred_locations: [],
      registration_source: 'Online Form'
    }
  });

  // Check for agent parameter in URL
  useEffect(() => {
    const checkAgentParam = async () => {
      const agentId = searchParams?.get('agent');
      const referralCode = searchParams?.get('ref');
      
      if (agentId) {
        // Direct agent ID
        const id = parseInt(agentId);
        if (!isNaN(id)) {
          setSelectedAgentId(id);
          setAgentNotification('Anda dirujuk oleh agent. Agent akan dipilih otomatis.');
        }
      } else if (referralCode) {
        // Referral code - need to lookup agent
        try {
          const agent = await AgentService.getAgentByReferralCode(referralCode);
          if (agent) {
            setSelectedAgentId(agent.id);
            setAgentNotification(`Anda dirujuk oleh ${agent.user.full_name}. Agent telah dipilih otomatis.`);
          } else {
            setAgentNotification('Kode rujukan tidak ditemukan. Anda dapat memilih agent secara manual.');
          }
        } catch (error) {
          console.error('Error looking up referral code:', error);
          setAgentNotification('Gagal memverifikasi kode rujukan. Anda dapat memilih agent secara manual.');
        }
      }
    };

    checkAgentParam();
  }, [searchParams]);

  // Auto-set whatsapp_number to phone if not provided
  const phoneValue = watch('phone');
  useEffect(() => {
    if (phoneValue && !watch('whatsapp_number')) {
      setValue('whatsapp_number', phoneValue);
    }
  }, [phoneValue, setValue, watch]);

  const nextStep = async () => {
    const fieldsToValidate = getFieldsForStep(currentStep);
    const isValid = await trigger(fieldsToValidate);
    
    if (isValid) {
      setCurrentStep(prev => Math.min(prev + 1, STEPS.length));
    }
  };

  const prevStep = () => {
    setCurrentStep(prev => Math.max(prev - 1, 1));
  };

  const getFieldsForStep = (step: number): (keyof FormData)[] => {
    switch (step) {
      case 1:
        return ['first_name', 'last_name', 'email', 'phone', 'nik', 'birth_date', 'birth_place', 'gender'];
      case 2:
        return ['address', 'city', 'province', 'whatsapp_number'];
      case 3:
        return ['education_level', 'school_name', 'graduation_year'];
      case 4:
        return [];
      case 5:
        return [];
      default:
        return [];
    }
  };

  const onSubmit = async (data: FormData) => {
    setIsSubmitting(true);
    setSubmitError('');
    
    try {
      console.log('Submitting registration:', data);
      
      await RegistrationService.registerApplicant(data);
      
      // Redirect to success page or login
      router.push('/login?message=Pendaftaran berhasil! Silakan login dengan email dan NIK sebagai password.');
      
    } catch (error: any) {
      console.error('Registration failed:', error);
      setSubmitError(error.message || 'Pendaftaran gagal');
    } finally {
      setIsSubmitting(false);
    }
  };

  const renderStepContent = () => {
    switch (currentStep) {
      case 1:
        return <PersonalInfoStep register={register} errors={errors} />;
      case 2:
        return <AddressContactStep register={register} errors={errors} />;
      case 3:
        return <EducationStep register={register} errors={errors} />;
      case 4:
        return <ExperienceSkillsStep register={register} errors={errors} control={control} />;
      case 5:
        return (
          <JobPreferencesStep 
            register={register} 
            errors={errors} 
            control={control} 
            setValue={setValue}
            selectedAgentId={selectedAgentId}
          />
        );
      default:
        return null;
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8 px-4">
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <div className="text-center mb-8">
          <div className="mx-auto h-16 w-16 bg-blue-600 rounded-2xl flex items-center justify-center mb-4">
            <UserPlus className="h-8 w-8 text-white" />
          </div>
          <h1 className="text-3xl font-bold text-gray-900 mb-2">
            Pendaftaran Pelamar Kerja
          </h1>
          <p className="text-gray-600">
            Lengkapi data diri Anda untuk bergabung dengan sistem penempatan kerja
          </p>
        </div>

        {/* Agent Notification */}
        {agentNotification && (
          <div className="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div className="flex items-start space-x-3">
              <AlertCircle className="h-5 w-5 text-blue-600 mt-0.5" />
              <p className="text-sm text-blue-700">{agentNotification}</p>
            </div>
          </div>
        )}

        {/* Progress Steps */}
        <div className="mb-8">
          <div className="flex items-center justify-between">
            {STEPS.map((step, index) => {
              const Icon = step.icon;
              const isActive = currentStep === step.id;
              const isCompleted = currentStep > step.id;
              
              return (
                <div key={step.id} className="flex items-center">
                  <div className={`flex items-center justify-center w-10 h-10 rounded-full border-2 ${
                    isCompleted 
                      ? 'bg-green-500 border-green-500' 
                      : isActive 
                        ? 'bg-blue-500 border-blue-500' 
                        : 'bg-white border-gray-300'
                  }`}>
                    {isCompleted ? (
                      <Check className="h-5 w-5 text-white" />
                    ) : (
                      <Icon className={`h-5 w-5 ${isActive ? 'text-white' : 'text-gray-400'}`} />
                    )}
                  </div>
                  <div className="ml-3 hidden md:block">
                    <p className={`text-sm font-medium ${
                      isActive ? 'text-blue-600' : isCompleted ? 'text-green-600' : 'text-gray-500'
                    }`}>
                      {step.name}
                    </p>
                  </div>
                  {index < STEPS.length - 1 && (
                    <div className={`hidden md:block w-16 h-1 mx-4 ${
                      isCompleted ? 'bg-green-500' : 'bg-gray-200'
                    }`} />
                  )}
                </div>
              );
            })}
          </div>
        </div>

        {/* Form */}
        <div className="bg-white rounded-2xl shadow-xl p-8">
          <form onSubmit={handleSubmit(onSubmit)}>
            {renderStepContent()}

            {/* Error Message */}
            {submitError && (
              <div className="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <p className="text-sm text-red-600">{submitError}</p>
              </div>
            )}

            {/* Navigation Buttons */}
            <div className="flex items-center justify-between mt-8 pt-6 border-t">
              <button
                type="button"
                onClick={prevStep}
                disabled={currentStep === 1}
                className={`flex items-center px-6 py-3 rounded-lg font-medium ${
                  currentStep === 1
                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                <ChevronLeft className="h-5 w-5 mr-2" />
                Sebelumnya
              </button>

              <div className="text-sm text-gray-500">
                Langkah {currentStep} dari {STEPS.length}
              </div>

              {currentStep < STEPS.length ? (
                <button
                  type="button"
                  onClick={nextStep}
                  className="flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700"
                >
                  Selanjutnya
                  <ChevronRight className="h-5 w-5 ml-2" />
                </button>
              ) : (
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {isSubmitting ? (
                    <>
                      <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                      Mendaftar...
                    </>
                  ) : (
                    <>
                      <Check className="h-5 w-5 mr-2" />
                      Daftar Sekarang
                    </>
                  )}
                </button>
              )}
            </div>
          </form>
        </div>

        {/* Footer */}
        <div className="text-center mt-8">
          <p className="text-gray-600">
            Sudah punya akun?{' '}
            <button
              onClick={() => router.push('/login')}
              className="text-blue-600 hover:text-blue-700 font-medium"
            >
              Masuk di sini
            </button>
          </p>
        </div>
      </div>
    </div>
  );
}
