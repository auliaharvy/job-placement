import React from 'react';
import { UseFormRegister, FieldErrors, Control, useFieldArray } from 'react-hook-form';
import { Plus, X } from 'lucide-react';
import { ApplicantRegistrationData } from '@/lib/registration';

interface ExperienceSkillsStepProps {
  register: UseFormRegister<ApplicantRegistrationData>;
  errors: FieldErrors<ApplicantRegistrationData>;
  control: Control<ApplicantRegistrationData>;
}

export default function ExperienceSkillsStep({ register, errors, control }: ExperienceSkillsStepProps) {
  const { fields: workFields, append: appendWork, remove: removeWork } = useFieldArray({
    control,
    name: 'work_experience'
  });

  const { fields: skillFields, append: appendSkill, remove: removeSkill } = useFieldArray({
    control,
    name: 'skills'
  });

  return (
    <div className="space-y-6">
      <h3 className="text-xl font-semibold text-gray-900 mb-6">Pengalaman Kerja & Keahlian</h3>
      
      {/* Work Experience */}
      <div>
        <div className="flex items-center justify-between mb-4">
          <h4 className="text-lg font-medium text-gray-900">Pengalaman Kerja</h4>
          <button
            type="button"
            onClick={() => appendWork({ company: '', position: '', years: 0 })}
            className="flex items-center px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            <Plus className="h-4 w-4 mr-1" />
            Tambah Pengalaman
          </button>
        </div>
        
        <div className="space-y-4">
          {workFields.map((field, index) => (
            <div key={field.id} className="p-4 border border-gray-200 rounded-lg">
              <div className="flex items-center justify-between mb-3">
                <h5 className="font-medium text-gray-900">Pengalaman {index + 1}</h5>
                <button
                  type="button"
                  onClick={() => removeWork(index)}
                  className="text-red-600 hover:text-red-800"
                >
                  <X className="h-4 w-4" />
                </button>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Perusahaan
                  </label>
                  <input
                    {...register(`work_experience.${index}.company` as const)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="PT ABC"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Posisi
                  </label>
                  <input
                    {...register(`work_experience.${index}.position` as const)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Software Engineer"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Lama (tahun)
                  </label>
                  <input
                    {...register(`work_experience.${index}.years` as const)}
                    type="number"
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="3"
                  />
                </div>
              </div>
            </div>
          ))}
          
          {workFields.length === 0 && (
            <div className="text-center py-8 text-gray-500">
              Belum ada pengalaman kerja. Klik "Tambah Pengalaman" untuk mulai menambahkan.
            </div>
          )}
        </div>
      </div>

      {/* Skills */}
      <div>
        <div className="flex items-center justify-between mb-4">
          <h4 className="text-lg font-medium text-gray-900">Keahlian</h4>
          <button
            type="button"
            onClick={() => appendSkill({ skill: '' })}
            className="flex items-center px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            <Plus className="h-4 w-4 mr-1" />
            Tambah Keahlian
          </button>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {skillFields.map((field, index) => (
            <div key={field.id} className="flex items-center space-x-2">
              <input
                {...register(`skills.${index}.skill` as const)}
                className="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="PHP, Laravel, dll."
              />
              <button
                type="button"
                onClick={() => removeSkill(index)}
                className="text-red-600 hover:text-red-800"
              >
                <X className="h-4 w-4" />
              </button>
            </div>
          ))}
        </div>
        
        {skillFields.length === 0 && (
          <div className="text-center py-8 text-gray-500">
            Belum ada keahlian. Klik "Tambah Keahlian" untuk mulai menambahkan.
          </div>
        )}
      </div>

      {/* Total Work Experience */}
      <div>
        <label className="block text-sm font-medium text-gray-700 mb-2">
          Total Pengalaman Kerja (bulan)
        </label>
        <input
          {...register('total_work_experience_months')}
          type="number"
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          placeholder="36"
        />
        <p className="mt-1 text-xs text-gray-500">Total bulan pengalaman kerja dari semua posisi</p>
      </div>
    </div>
  );
}
