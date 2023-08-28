import type { ProjectCodec } from '@/services/api/resources/project/codec'

export function assertIsProjectCodec(
  project: ProjectCodec | null
): asserts project is ProjectCodec {
  if (project === null) throw new Error('`project` should not be `null`')
}
