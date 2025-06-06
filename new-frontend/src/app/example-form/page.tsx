import { Suspense } from 'react';
import ExampleForm from '@/components/forms/example-form';

export default function ExampleFormPage() {
  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <Suspense fallback={<div className="text-center">Loading...</div>}>
        <ExampleForm />
      </Suspense>
    </div>
  );
}