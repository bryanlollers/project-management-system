import { test } from "node:test";
import assert from "node:assert/strict";
import { buildResourcePayload, createWorkspaceForm } from "../utils/forms.ts";
import { getErrorMessage, isUnauthorized } from "../utils/errors.ts";
import { formatDate, formatLabel, getInitials } from "../utils/format.ts";
import { canEditResource, canUpdateTask } from "../utils/permissions.ts";
import type { Person } from "../types/entities.ts";
import { getLoginRedirect, WORKSPACE_PATHS } from "../utils/navigation.ts";

test("workspace routes are distinct and login redirects stay inside workspace", () => {
  assert.equal(new Set(Object.values(WORKSPACE_PATHS)).size, 6);
  assert.equal(WORKSPACE_PATHS.users, "/team");
  assert.equal(getLoginRedirect("/tasks?record=7"), "/tasks?record=7");
  for (const value of ["https://example.com", "//example.com", "/tasksmith", null, ["/clients"]]) {
    assert.equal(getLoginRedirect(value), "/dashboard");
  }
});

test("editing contacts and team members does not mutate loaded record data", () => {
  const record = {
    id: 1,
    name: "Client",
    contacts: [{ name: "Pat", email: "pat@example.com" }],
    members: [{ id: 7, name: "Pat", email: "pat@example.com", role: "staff" as const }],
  };
  const form = createWorkspaceForm("clients", record);
  form.contacts[0].name = "Updated";
  form.member_ids.push(8);
  assert.equal(record.contacts[0].name, "Pat");
  assert.deepEqual(
    record.members.map((member) => member.id),
    [7],
  );
});

test("task payloads include task fields, normalize empty relations, and exclude account fields", () => {
  const form = createWorkspaceForm("tasks", undefined, { projectId: 12 });
  form.title = "Ship release";
  form.password = "should-never-be-sent";
  const payload = buildResourcePayload("tasks", form, false);
  assert.equal(payload.project_id, 12);
  assert.equal(payload.title, "Ship release");
  assert.equal(payload.assignee_id, null);
  assert.equal(payload.due_date, null);
  assert.equal("password" in payload, false);
  assert.equal("member_ids" in payload, false);
});

test("editing an account preserves its password unless a replacement is entered", () => {
  const form = createWorkspaceForm("users");
  form.name = "Jamie";
  form.email = "jamie@example.com";
  assert.equal("password" in buildResourcePayload("users", form, true), false);
  form.password = "NewPassword!2026";
  assert.equal(buildResourcePayload("users", form, true).password, "NewPassword!2026");
});

test("API validation errors and authentication failures handle unknown input safely", () => {
  assert.equal(
    getErrorMessage({
      data: {
        errors: { email: ["Email is required."], name: ["Name is required."] },
      },
    }),
    "Email is required. Name is required.",
  );
  assert.equal(getErrorMessage(new Error("Connection failed")), "Connection failed");
  assert.equal(getErrorMessage(null), "Something went wrong. Please try again.");
  assert.equal(isUnauthorized({ statusCode: 401 }), true);
  assert.equal(isUnauthorized({ status: 403 }), false);
});

test("display helpers handle empty names and missing dates", () => {
  assert.equal(getInitials(" Alex  Morgan "), "AM");
  assert.equal(getInitials("   "), "?");
  assert.equal(formatDate(null), "—");
  assert.equal(formatLabel("in_progress"), "in progress");
});

test("staff controls allow own task updates and reserve account editing for admins", () => {
  const staff: Person = {
    id: 3,
    name: "Taylor",
    email: "taylor@example.com",
    role: "staff",
  };
  const manager: Person = { ...staff, role: "manager" };
  assert.equal(canEditResource(staff, "projects"), false);
  assert.equal(canEditResource(manager, "projects"), true);
  assert.equal(canEditResource(manager, "users"), false);
  assert.equal(canUpdateTask(staff, { id: 1, assignee_id: 3 }), true);
  assert.equal(canUpdateTask(staff, { id: 2, assignee_id: 4 }), false);
});
