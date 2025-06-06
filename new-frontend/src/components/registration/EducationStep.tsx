import React from 'react';
import { UseFormRegister, FieldErrors } from 'react-hook-form';
import { ApplicantRegistrationData } from '@/lib/registration';

interface EducationStepProps {
  register: UseFormRegister<ApplicantRegistrationData>;
  errors: FieldErrors<ApplicantRegistrationData>;
}

export default function EducationStep({ register, errors }: EducationStepProps) {
  return (
    <div className="space-y-6">
      <h3 className="text-xl font-semibold text-gray-900 mb-6">Informasi Pendidikan</h3>
      
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Tingkat Pendidikan *
          </label>
          <select
            {...register('education_level', { required: 'Tingkat pendidikan wajib dipilih' })}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="sd">SD</option>
            <option value="smp">SMP</option>
            <option value="sma">SMA</option>
            <option value="smk">SMK</option>
            <option value="d1">D1</option>
            <option value="d2">D2</option>
            <option value="d3">D3</option>
            <option value="s1">S1</option>
            <option value="s2">S2</option>
            <option value="s3">S3</option>
          </select>
          {errors.education_level && (
            <p className="mt-1 text-sm text-red-600">{errors.education_level.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Nama Sekolah/Universitas *
          </label>
          <input
            {...register('school_name', { required: 'Nama sekolah wajib diisi' })}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Universitas Indonesia"
          />
          {errors.school_name && (
            <p className="mt-1 text-sm text-red-600">{errors.school_name.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Jurusan/Bidang Studi
          </label>
          <input
            {...register('major')}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Teknik Informatika"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Tahun Lulus *
          </label>
          <input
            {...register('graduation_year', { 
              required: 'Tahun lulus wajib diisi',
              min: { value: 1990, message: 'Tahun harus 1990 atau setelahnya' },
              max: { value: new Date().getFullYear(), message: 'Tahun tidak boleh di masa depan' }
            })}
            type="number"
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="2017"
          />
          {errors.graduation_year && (
            <p className="mt-1 text-sm text-red-600">{errors.graduation_year.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            IPK (skala 0-4)
          </label>
          <input
            {...register('gpa', { 
              min: { value: 0, message: 'IPK minimal 0' },
              max: { value: 4, message: 'IPK maksimal 4' }
            })}
            type="number"
            step="0.01"
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="3.75"
          />
          {errors.gpa && (
            <p className="mt-1 text-sm text-red-600">{errors.gpa.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Golongan Darah
          </label>
          <select
            {...register('blood_type')}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">Pilih Golongan Darah</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="AB">AB</option>
            <option value="O">O</option>
          </select>
        </div>
      </div>
    </div>
  );
}
