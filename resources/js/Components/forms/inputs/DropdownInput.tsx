import { FieldErrors } from "react-hook-form";

interface DropdownProps {
    label: string;
    interfaceRef: string;
    required: boolean;
    errors: FieldErrors<any>;
    register: Function;
    enumType: Record<string, string>;
}

export default function DropdownInput({
    label,
    interfaceRef,
    required,
    errors,
    register,
    enumType,
}: DropdownProps) {
    return (
        <label className="form-control w-full">
            <div className="label">
                <span className="label-text">{label}</span>
            </div>
            <select
                className="select select-bordered"
                {...register(interfaceRef, {
                    required: {
                        value: required,
                        message: `${label} is required`,
                    },
                })}
            >
                {Object.entries(enumType).map(([key, value]) => (
                    <option key={key} value={value}>
                        {value}
                    </option>
                ))}
            </select>
            <div className="text-error text-sm">
                {errors[interfaceRef]?.message?.toString()}
            </div>
        </label>
    );
}
